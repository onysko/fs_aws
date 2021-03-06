#SamsonPHP AWS File service module

This is File service implementation for Amazon AWS S3 buckets in SamsonPHP.
This is abstraction layer over standard PHP file functions. 
 
[![Latest Stable Version](https://poser.pugx.org/samsonphp/fs_aws/v/stable.svg)](https://packagist.org/packages/samsonphp/fs_aws)
[![Build Status](https://travis-ci.org/SamsonPHP/fs_aws.svg?branch=master)](https://travis-ci.org/SamsonPHP/fs_aws)
[![Code Coverage](https://scrutinizer-ci.com/g/samsonphp/fs_aws/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/samsonphp/fs_aws/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/samsonphp/fs_aws/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/samsonphp/fs_aws/?branch=master)
[![Stories in Ready](https://badge.waffle.io/samsonphp/fs_aws.png?label=ready&title=Ready)](https://waffle.io/samsonphp/fs_aws) 
[![Total Downloads](https://poser.pugx.org/samsonphp/fs_aws/downloads.svg)](https://packagist.org/packages/samsonphp/fs_aws)

##Configuration  

This is done using [SamsonPHP configuration system](https://github.com/samsonphp/config)

> In all nested php_fs_* modules which must be build on top of php_fs module all configuration are done to main [SamsonPHP File service module](https://github.com/samsonphp/fs)(It's identifier is ```fs```). This configuration class field values will be automatically passed to nested AbstractFileService ancestor.

All available configuration fields are:
```php
class FSConfig extends \samson\core\Config 
{
  /**@var string Set Amazon Web Services as web-application file service using its class name */
  public $fileServiceClassName = 'samsonphp\fs\AWSFileService';

  /** @var string $bucket Aws bucket name */
  public $bucket = '...';
 
  /** @var string $accessKey */
  public $accessKey = '...';
 
  /** @var string $secretKey */
  public $secretKey = '...';
 
  /** @var string $bucketURL Url of amazon bucket */
  public $bucketURL = '...';
  
  /** @var string $region Region of AWS S3 service */
  public $region = '...';
}
