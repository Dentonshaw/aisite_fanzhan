<?php
class FileCache {
    private static $cache = [];
    private static $fileTimes = [];
    private static $cacheDir = null;
    private static $initialized = false;
    private static $timeout = 2;

    const CACHE_META_FILE = 'filecache_meta.php';
    const CACHE_DATA_FILE = 'filecache_data.php';
    const CACHE_LOCK_FILE = 'filecache.lock';
    const CACHE_CHUNK_SIZE = 10000;
    const MAX_FILE_SIZE = 5242880;
    const MAX_SERIALIZE_SIZE = 1048576;
    
    // 设置超时时间
    public static function setTimeout($seconds) {
        self::$timeout = $seconds;
    }
    
    private static function init() {
        if (self::$initialized) {
            return;
        }
        
        if (self::$cacheDir === null) {
            $tempDir = sys_get_temp_dir();
            self::$cacheDir = rtrim($tempDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'php_filecache' . DIRECTORY_SEPARATOR;
            
            if (!is_dir(self::$cacheDir) && !mkdir(self::$cacheDir, 0777, true)) {
                self::$cacheDir = __DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
                if (!is_dir(self::$cacheDir) && !mkdir(self::$cacheDir, 0777, true)) {
                    self::$cacheDir = false;
                }
            }
        }
        
        self::$initialized = true;
        register_shutdown_function([__CLASS__, 'cleanupLocks']);
    }
    
    public static function get($filePath) {
        if (!file_exists($filePath)) {
            return [];
        }
        
        $currentModified = @filemtime($filePath);
        if ($currentModified === false) {
            return [];
        }
        
        if (isset(self::$fileTimes[$filePath]) && 
            self::$fileTimes[$filePath] >= $currentModified &&
            isset(self::$cache[$filePath])) {
            return self::$cache[$filePath];
        }
        
        $sharedCache = self::getFromSharedCache($filePath, $currentModified);
        if ($sharedCache !== false) {
            self::$cache[$filePath] = $sharedCache;
            self::$fileTimes[$filePath] = $currentModified;
            return $sharedCache;
        }
        
        $data = self::readFileWithLock($filePath, $currentModified);
        self::$cache[$filePath] = $data;
        self::$fileTimes[$filePath] = $currentModified;
        
        return $data;
    }
    
    public static function getLines($filePath, $startLine, $endLine) {
        if (!file_exists($filePath)) {
            return [];
        }
        
        $currentModified = @filemtime($filePath);
        if ($currentModified === false) {
            return [];
        }
        
        // 检查文件大小
        $fileSize = @filesize($filePath);
        if ($fileSize > self::MAX_FILE_SIZE) {
            return self::readFileChunkDirectly($filePath, 1); // 只读取第一块
        }
        
        $chunkStart = floor(($startLine - 1) / self::CACHE_CHUNK_SIZE) + 1;
        $chunkEnd = floor(($endLine - 1) / self::CACHE_CHUNK_SIZE) + 1;
        
        $result = [];
        
        for ($chunk = $chunkStart; $chunk <= $chunkEnd; $chunk++) {
            $chunkData = self::getFileChunk($filePath, $chunk, $currentModified);
            
            if (!empty($chunkData)) {
                $chunkFirstLine = ($chunk - 1) * self::CACHE_CHUNK_SIZE + 1;
                $chunkLastLine = $chunk * self::CACHE_CHUNK_SIZE;
                
                $localStart = max($startLine, $chunkFirstLine) - $chunkFirstLine;
                $localEnd = min($endLine, $chunkLastLine) - $chunkFirstLine;
                
                $lines = array_slice($chunkData, $localStart, $localEnd - $localStart);
                $result = array_merge($result, $lines);
            }
        }
        
        return array_filter($result, function($line) {
            return trim($line) !== '';
        });
    }
    
    private static function getFileChunk($filePath, $chunkNumber, $fileMTime) {
        $cacheKey = self::getChunkCacheKey($filePath, $chunkNumber);
        
        if (isset(self::$fileTimes[$cacheKey]) && 
            self::$fileTimes[$cacheKey] >= $fileMTime &&
            isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }
        
        $sharedCache = self::getChunkFromSharedCache($filePath, $chunkNumber, $fileMTime);
        if ($sharedCache !== false) {
            self::$cache[$cacheKey] = $sharedCache;
            self::$fileTimes[$cacheKey] = $fileMTime;
            return $sharedCache;
        }
        
        $chunkData = self::readFileChunkWithLock($filePath, $chunkNumber, $fileMTime);
        self::$cache[$cacheKey] = $chunkData;
        self::$fileTimes[$cacheKey] = $fileMTime;
        
        return $chunkData;
    }
    
    private static function getFromSharedCache($filePath, $currentModified) {
        self::init();
        
        if (self::$cacheDir === false) {
            return false;
        }
        
        $cacheKey = self::getCacheKey($filePath);
        $metaFile = self::$cacheDir . self::CACHE_META_FILE;
        $dataFile = self::$cacheDir . self::CACHE_DATA_FILE;
        
        if (!file_exists($metaFile) || !file_exists($dataFile)) {
            return false;
        }
        
        $metaData = self::readAtomicFile($metaFile);
        if ($metaData === false || !isset($metaData[$cacheKey])) {
            return false;
        }
        
        $cacheMeta = $metaData[$cacheKey];
        
        if ($cacheMeta['mtime'] < $currentModified || 
            $cacheMeta['expire'] < time()) {
            return false;
        }
        
        $cacheData = self::readAtomicFile($dataFile);
        if ($cacheData === false || !isset($cacheData[$cacheKey])) {
            return false;
        }
        
        return $cacheData[$cacheKey];
    }
    
    private static function getChunkFromSharedCache($filePath, $chunkNumber, $fileMTime) {
        self::init();
        
        if (self::$cacheDir === false) {
            return false;
        }
        
        $cacheKey = self::getChunkCacheKey($filePath, $chunkNumber);
        $metaFile = self::$cacheDir . self::CACHE_META_FILE;
        $dataFile = self::$cacheDir . self::CACHE_DATA_FILE;
        
        if (!file_exists($metaFile) || !file_exists($dataFile)) {
            return false;
        }
        
        $metaData = self::readAtomicFile($metaFile);
        if ($metaData === false || !isset($metaData[$cacheKey])) {
            return false;
        }
        
        $cacheMeta = $metaData[$cacheKey];
        
        if ($cacheMeta['mtime'] < $fileMTime || 
            $cacheMeta['expire'] < time()) {
            return false;
        }
        
        $cacheData = self::readAtomicFile($dataFile);
        if ($cacheData === false || !isset($cacheData[$cacheKey])) {
            return false;
        }
        
        return $cacheData[$cacheKey];
    }
    
    private static function readFileWithLock($filePath, $currentModified) {
        self::init();
        
        if (self::$cacheDir === false) {
            return self::readFileDirectly($filePath);
        }
        
        $cacheKey = self::getCacheKey($filePath);
        $lockFile = self::$cacheDir . $cacheKey . '.lock';
        
        $lockHandle = @fopen($lockFile, 'w+');
        if (!$lockHandle) {
            return self::readFileDirectly($filePath);
        }
        
        $startTime = microtime(true);
        $timeout = self::$timeout;
        
        // 非阻塞锁获取
        while (!flock($lockHandle, LOCK_EX | LOCK_NB)) {
            if (microtime(true) - $startTime > $timeout) {
                fclose($lockHandle);
                return self::readFileDirectly($filePath);
            }
            usleep(10000);
        }
        
        try {
            $sharedCache = self::getFromSharedCache($filePath, $currentModified);
            if ($sharedCache !== false) {
                return $sharedCache;
            }
            
            $content = self::readFileDirectly($filePath);
            
            // 检查数据大小
            if (is_array($content) && count($content) > self::CACHE_CHUNK_SIZE) {
                $content = array_slice($content, 0, self::CACHE_CHUNK_SIZE);
            }
            
            self::updateSharedCache($cacheKey, $content, $currentModified);
            
            return $content;
        } finally {
            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);
            @unlink($lockFile);
        }
    }
    
    private static function readFileChunkWithLock($filePath, $chunkNumber, $fileMTime) {
        self::init();
        
        if (self::$cacheDir === false) {
            return self::readFileChunkDirectly($filePath, $chunkNumber);
        }
        
        $cacheKey = self::getChunkCacheKey($filePath, $chunkNumber);
        $lockFile = self::$cacheDir . $cacheKey . '.lock';
        
        $lockHandle = @fopen($lockFile, 'w+');
        if (!$lockHandle) {
            return self::readFileChunkDirectly($filePath, $chunkNumber);
        }
        
        $startTime = microtime(true);
        $timeout = self::$timeout;
        
        // 非阻塞锁获取
        while (!flock($lockHandle, LOCK_EX | LOCK_NB)) {
            if (microtime(true) - $startTime > $timeout) {
                fclose($lockHandle);
                return self::readFileChunkDirectly($filePath, $chunkNumber);
            }
            usleep(10000);
        }
        
        try {
            $sharedCache = self::getChunkFromSharedCache($filePath, $chunkNumber, $fileMTime);
            if ($sharedCache !== false) {
                return $sharedCache;
            }
            
            $chunkData = self::readFileChunkDirectly($filePath, $chunkNumber);
            self::updateSharedCache($cacheKey, $chunkData, $fileMTime);
            
            return $chunkData;
        } finally {
            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);
            @unlink($lockFile);
        }
    }
    
    private static function readFileDirectly($filePath) {
        $content = @file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return $content !== false ? $content : [];
    }
    
    private static function readFileChunkDirectly($filePath, $chunkNumber) {
        $startLine = ($chunkNumber - 1) * self::CACHE_CHUNK_SIZE;
        $endLine = $chunkNumber * self::CACHE_CHUNK_SIZE;
        
        $content = [];
        $fp = new SplFileObject($filePath, 'rb');
        
        if ($startLine > 0) {
            $fp->seek($startLine - 1);
        }
        
        for ($i = $startLine; $i < $endLine && !$fp->eof(); $i++) {
            $line = $fp->current();
            if ($line !== false) {
                $content[] = $line;
            }
            $fp->next();
        }
        
        return $content;
    }
    
    private static function updateSharedCache($cacheKey, $content, $mtime) {
        if (self::$cacheDir === false) {
            return;
        }
        
        $metaFile = self::$cacheDir . self::CACHE_META_FILE;
        $dataFile = self::$cacheDir . self::CACHE_DATA_FILE;
        $lockFile = self::$cacheDir . self::CACHE_LOCK_FILE;
        
        $lockHandle = @fopen($lockFile, 'w+');
        if (!$lockHandle) {
            return;
        }
        
        $startTime = microtime(true);
        $timeout = self::$timeout;
        
        // 非阻塞锁获取
        while (!flock($lockHandle, LOCK_EX | LOCK_NB)) {
            if (microtime(true) - $startTime > $timeout) {
                fclose($lockHandle);
                return;
            }
            usleep(10000);
        }
        
        try {
            $metaData = self::readAtomicFile($metaFile) ?: [];
            $cacheData = self::readAtomicFile($dataFile) ?: [];
            
            // 检查数据大小
            $serialized = serialize($content);
            if (strlen($serialized) > self::MAX_SERIALIZE_SIZE) {
                $content = array_slice($content, 0, self::CACHE_CHUNK_SIZE);
                $serialized = serialize($content);
            }
            
            $metaData[$cacheKey] = [
                'mtime' => $mtime,
                'expire' => time() + 7200,
                'size' => strlen($serialized)
            ];
            
            $cacheData[$cacheKey] = $content;
            
            // 限制缓存大小
            if (count($metaData) > 1000) {
                uasort($metaData, function($a, $b) {
                    return $a['mtime'] - $b['mtime'];
                });
                $metaData = array_slice($metaData, -1000, 1000, true);
                $cacheData = array_intersect_key($cacheData, $metaData);
            }
            
            self::writeAtomicFile($metaFile, $metaData);
            self::writeAtomicFile($dataFile, $cacheData);
            
        } finally {
            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);
        }
    }
    
    private static function readAtomicFile($filePath) {
        if (!file_exists($filePath)) {
            return false;
        }
        
        $content = @file_get_contents($filePath);
        if ($content === false) {
            return false;
        }
        
        if (strpos($content, '<?php') === 0) {
            $content = substr($content, 5);
            if (substr($content, -2) === '?>') {
                $content = substr($content, 0, -2);
            }
            $content = trim($content);
            
            if (substr($content, 0, 6) === 'return') {
                $content = substr($content, 6);
            }
        }
        
        $data = @unserialize($content);
        return $data !== false ? $data : false;
    }
    
    private static function writeAtomicFile($filePath, $data) {
        $tempFile = $filePath . '.' . uniqid('tmp_', true);
        $content = '<?php return ' . var_export($data, true) . ';';
        
        if (@file_put_contents($tempFile, $content, LOCK_EX) !== false) {
            if (@rename($tempFile, $filePath)) {
                @chmod($filePath, 0666);
                return true;
            }
        }
        
        @unlink($tempFile);
        return false;
    }
    
    private static function getCacheKey($filePath) {
        return md5(realpath($filePath) ?: $filePath);
    }
    
    private static function getChunkCacheKey($filePath, $chunkNumber) {
        return md5((realpath($filePath) ?: $filePath) . '_chunk_' . $chunkNumber);
    }
    
    public static function cleanupLocks() {
        if (self::$cacheDir === false || !is_dir(self::$cacheDir)) {
            return;
        }
        
        $files = @scandir(self::$cacheDir);
        if (!$files) {
            return;
        }
        
        $now = time();
        foreach ($files as $file) {
            if (substr($file, -5) === '.lock') {
                $lockFile = self::$cacheDir . $file;
                $mtime = @filemtime($lockFile);
                if ($mtime && ($now - $mtime) > 300) {
                    @unlink($lockFile);
                }
            }
        }
    }
}