<?php 
use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ArrayCache,
    Doctrine\DBAL\Logging\EchoSQLLogger,
    Doctrine\ORM\Mapping\Driver\DatabaseDriver,
    Doctrine\ORM\Tools\DisconnectedClassMetadataFactory,
    Doctrine\ORM\Tools\EntityGenerator;
    /**
     * CodeIgniter Doctrine Class
     *
     * initializes basic doctrine settings and act as doctrine object
     *
     * @author	Mehmet Aydın Bahadır
     */
    class Doctrine {

          /**
           * @var EntityManager $em
           */
            public $em = null;
            
          /**
           * constructor
           */
          public function __construct()
          { 
            // Set up class loading. You could use different autoloaders, provided by your favorite framework,
            // if you want to.
            require_once __DIR__.'/vendor/Doctrine/Common/ClassLoader.php';
            
            $doctrineClassLoader = new ClassLoader('Doctrine',  __DIR__.'/vendor');
            $doctrineClassLoader->register();
            $entitiesClassLoader = new ClassLoader('models', __DIR__);
            $entitiesClassLoader->register();
            $proxiesClassLoader = new ClassLoader('proxies', __DIR__.'/models');
            $proxiesClassLoader->register();
        
            // Set up caches
            $config = new Configuration;
            $cache = new ArrayCache;
            $config->setMetadataCacheImpl($cache);
            $driverImpl = $config->newDefaultAnnotationDriver(array(__DIR__.'/models/entities'));
            $config->setMetadataDriverImpl($driverImpl);
            $config->setQueryCacheImpl($cache);
        
            // Proxy configuration
            $config->setProxyDir(__DIR__.'/models/proxies');
            $config->setProxyNamespace('Proxies');
        
            // Set up logger
            //$logger = new EchoSQLLogger;
            //$config->setSQLLogger($logger);
        
            $config->setAutoGenerateProxyClasses( TRUE );

            // Database connection information
            $connectionOptions = array(
                'driver' =>   'pdo_mysql',
                'user' =>     'root',
                'password' => '',
                'host' =>     'localhost',
                'dbname' =>   ''
            );
        
            // Create EntityManager
            $this->em = EntityManager::create($connectionOptions, $config);
            
            //$this->generate_classes();
          }
          
          /**
           * generate entity objects automatically from mysql db tables
           * @return none
           */
          public function generate_classes(){     

            $this->em->getConfiguration()
                     ->setMetadataDriverImpl(
                        new DatabaseDriver(
                                $this->em->getConnection()->getSchemaManager()
                        )
            );

            $cmf = new DisconnectedClassMetadataFactory();          
            $cmf->setEntityManager($this->em);        
            $metadata = $cmf->getAllMetadata();   
                  
            $generator = new EntityGenerator();
            $generator->setUpdateEntityIfExists(true);
            $generator->setGenerateStubMethods(true);
            $generator->setGenerateAnnotations(true);
            $generator->generate($metadata, __DIR__."/models/entities");
          }
    
    }
?>