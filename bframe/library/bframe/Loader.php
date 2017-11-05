<?php
/**
 * 框架类自动加载实现
 */
namespace bframe;

class Loader
{
    protected static $instance = [];

    // 类名映射缓存数组
    protected static $map = [];

    // 命名空间别名
    protected static $alias = [];

    // PSR-4
    private static $prefixLengthsPsr4 = [];
    private static $prefixDirsPsr4    = [];
    private static $fallbackDirsPsr4  = [];

    // PSR-0
    private static $prefixesPsr0     = [];
    private static $fallbackDirsPsr0 = [];

    // 自动加载的文件
    private static $autoloadFiles = [];

    public static function register($autoload = '')
    {
        // 注册系统自动加载
        spl_autoload_register($autoload ? : 'bframe\\Loader::autoload', true, true);

        // 注册命名空间定义
        self::addNamespace([
            'bframe' => BFRAME_LIB_PATH . 'bframe' . DS,
        ]);

        // 加载类库映射文件
        if (is_file(RUNTIME_PATH . 'classmap' . EXT)) {
            self::addClassMap(__include_file(RUNTIME_PATH . 'classmap' . EXT));
        }

        //Composer自动加载
        if (is_dir(VENDOR_PATH . 'composer')) {
            self::registerComposerLoader();
        }
    }

    /**
     * 注册Composer自动注册
     */
    private static function registerComposerLoader()
    {
        if (is_file(VENDOR_PATH . 'composer/autoload_namespaces.php')) {
            $map = require VENDOR_PATH . 'composer/autoload_banespaces.php';
            foreach ($map as $namespace => $path) {
                self::addPsr0($namespace, $path);
            }
        }

        if (is_file(VENDOR_PATH . 'composer/autoload_classmap.php')) {
            $classMap = require VENDOR_PATH . 'composer/autoload_clasmap.php';
            if ($classMap) {
                self::addClassMap($classMap);
            }
        }

        if (is_file(VENDOR_PATH . 'composer/autoload_files.php')) {
            $includeFiles = require VENDOR_PATH . 'composer/autoload_files.php';
            foreach ($includeFiles as $fileIdentifier => $file) {
                if (empty(self::$autoloadFiles[$fileIdentifier])) {
                    __require_file($file);
                    self::$autoloadFiles[$fileIdentifier] = true;
                }
            }
        }
    }

    public static function autoload($className)
    {
        // 检测命名空间别名
        if (! empty(self::$alias)) {
            $namespace = dirname($className);
            if (isset(self::$alias[$namespace])) {
                $orginal = self::$alias[$namespace] . '\\' . basename($className);
                if (class_exists($orginal)) {
                    return class_alias($orginal, $className, false);
                }
            }
        }

        if ($file = self::findFile($className)) {
            // Win环境严格区分大小写
            if (IS_WIN && pathinfo($file, PATHINFO_FILENAME) != pathinfo(realpath($file), PATHINFO_FILENAME)) {
                return false;
            }

            __include_file($file);
            return true;
        }
    }

    /**
     * 查找文件
     * @param $className
     * @return mixed
     */
    private static function findFile($className)
    {
        if (! empty(self::$map[$className])) {
            // 类库识别
            return self::$map[$className];
        }

        if (is_file($file = self::findPsr4File($className)) || is_file($file = self::findPsr0File($className))) {
            return $file;
        }

        return self::$map[$className] = false;
    }

    /**
     * 查找 PSR-4
     * @param $className
     * @return string
     */
    private static function findPsr4File($className)
    {
        $logicalPath = strtr($className, '\\', DS) . EXT;
        $first = $className[0];
        if (isset(self::$prefixDirsPsr4[$first])) {
            foreach (self::$prefixLengthsPsr4[$first] as $prefix => $length) {
                if (0 === strpos($className, $prefix)) {
                    foreach (self::$prefixDirsPsr4[$prefix] as $dir) {
                        if (is_file($file = $dir . DS . substr($logicalPath, $length))) {
                            return $file;
                        }
                    }
                }
            }
        }

        // 查找 PSR-4 fallback dirs
        foreach (self::$fallbackDirsPsr4 as $dir) {
            if (is_file($file = $dir . $logicalPath)) {
                return $file;
            }
        }
    }

    /**
     * 查找 PSR-0
     * @param $className
     * @return string
     */
    private static function findPsr0File($className)
    {
        $logicalPath = strtr($className, '\\', DS) . EXT;
        $first = $className[0];
        if (false !== $pos = strrpos($className, '\\')) {
            // namespaced class name
            $logicalPathPsr0 = substr($logicalPath, 0, $pos + 1)
                . strtr(substr($logicalPath, $pos + 1), '_', DS);
        } else {
            // PEAR-like class name
            $logicalPathPsr0 = strtr($className, '_', DS) . EXT;
        }

        if (isset(self::$prefixesPsr0[$first])) {
            foreach (self::$prefixesPsr0[$first] as $prefix => $dirs) {
                if (0 === strpos($className, $prefix)) {
                    foreach ($dirs as $dir) {
                        if (is_file($file = $dir . DS . $logicalPathPsr0)) {
                            return $file;
                        }
                    }
                }
            }
        }

        // 查找 PSR-0 fallback dirs
        foreach (self::$fallbackDirsPsr0 as $dir) {
            if (is_file($file = $dir . DS . $logicalPathPsr0)) {
                return $file;
            }
        }
    }

    public static function addNamespace($namespace, $path = '')
    {
        if (is_array($namespace)) {
            foreach ($namespace as $prefix => $paths) {
                self::addPsr4($prefix . '\\', rtrim($path, DS), true);
            }
        } else {
            self::addPsr4($namespace . '\\', rtrim($path, DS), true);
        }
    }

    /**
     * 添加Psr0空间
     *
     * @param      $prefix
     * @param      $paths
     * @param bool $prepend
     */
    private static function addPsr0($prefix, $paths, $prepend = false)
    {
        if (! $prefix) {
            if ($prepend) {
                self::$fallbackDirsPsr0 = array_merge(
                    (array) $paths,
                    self::$fallbackDirsPsr0
                );
            } else {
                self::$fallbackDirsPsr0 = array_merge(
                    self::$fallbackDirsPsr0,
                    (array) $paths
                );
            }

            return;
        }

        $first = $prefix[0];
        if (! isset(self::$prefixesPsr0[$first][$prefix])) {
            self::$prefixesPsr0[$first][$prefix] = (array) $paths;
            return;
        }
        if ($prepend) {
            self::$prefixesPsr0[$first][$prefix] = array_merge(
                (array) $paths,
                self::$prefixesPsr0[$first][$prefix]
            );
        } else {
            self::$prefixesPsr0[$first][$prefix] = array_merge(
                self::$prefixesPsr0[$first][$prefix],
                (array) $paths
            );
        }

    }

    /**
     * 添加Psr4空间
     *
     * @param      $prefix
     * @param      $paths
     * @param bool $prepend
     */
    private static function addPsr4($prefix, $paths, $prepend = false)
    {
        if (! $prefix) {
            // 为根命名空间注册目录
            if ($prepend) {
                self::$fallbackDirsPsr4 = array_merge(
                    (array) $paths,
                    self::$fallbackDirsPsr4
                );
            } else {
                self::$fallbackDirsPsr4 = array_merge(
                  self::$fallbackDirsPsr4,
                  (array) $paths
                );
            }
        } elseif (! isset(self::$prefixDirsPsr4[$prefix])) {
            // 为一个新的命名空间注册目录
            $length = strlen($prefix);
            if ('\\' != $prefix[$length - 1]) {
                throw new \InvalidArgumentException("A non-empty PSR-4 prefix must end with a namespace separator.");
            }
            self::$prefixLengthsPsr4[$prefix[0]][$prefix] = $length;
            self::$prefixDirsPsr4[$prefix] = (array) $paths;
        } elseif ($prepend) {
            self::$prefixDirsPsr4[$prefix] = array_merge(
                (array) $paths,
                self::$prefixDirsPsr4[$prefix]
            );
        } else {
            // 为已经注册的命名空间新增目录
            self::$prefixDirsPsr4[$prefix] = array_merge(
                self::$prefixDirsPsr4[$prefix],
                (array) $paths
            );
        }
    }

    public static function addClassMap($className, $map = '')
    {
        if (is_array($className)) {
            self::$map = array_merge(self::$map, $className);
        } else {
            self::$map[$className] = $map;
        }
    }
}

/**
 * 作用范围隔离
 *
 * @param $file
 * @return mixed
 */
function __include_file($file)
{
    return include $file;
}

function __require_file($file)
{
    return require $file;
}