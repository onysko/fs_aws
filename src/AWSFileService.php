<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 17.11.2014
 * Time: 14:12
 */
namespace samsonphp\fs;

use Aws\S3\S3Client;
use Aws\Common\Credentials\Credentials;

/**
 * Amazon Web Services Adapter implementation
 * @package samson\upload
 */
class AWSFileService extends AbstractFileService
{
    /** @var S3Client $client Aws services user */
    protected $client;

    /** @var string $bucket Aws bucket name */
    public $bucket;

    /** @var string $accessKey */
    public $accessKey;

    /** @var string $secretKey */
    public $secretKey;

    /** @var string $bucketURL Url of amazon bucket */
    public $bucketURL;

    /** @var string $region Region of amazon S3 service */
    public $region;

    /** @var int Resource caching age */
    public $maxAge = 1296000;

    /**
     * @param null $client AWSClient Object
     */
    public function __construct($client = null)
    {
        // Get client object instance from input parameters
        $this->client = & $client;
    }

    /**
     * Initialization stage
     */
    public function initialize()
    {
        // No client is passed
        if (!isset($this->client)) {
            // Use S3 clients, create authorization object and instantiate the S3 client with AWS credentials
            $this->client = S3Client::factory(array(
                'credentials' => new Credentials($this->accessKey, $this->secretKey),
                'region' => $this->region
            ));
        }
    }

    /**
     * Write data to a specific relative location
     *
     * @param mixed $data Data to be written
     * @param string $filename File name
     * @param string $uploadDir Relative file path
     * @return string Relative path to created file, false if there were errors
     */
    public function write($data, $filename = '', $uploadDir = '')
    {
        // Upload data to Amazon S3
        $this->client->putObject(array(
            'Bucket'       => $this->bucket,
            'Key'          => $uploadDir.'/'.$filename,
            'Body'         => $data,
            'CacheControl' => 'max-age='.$this->maxAge,
            'ACL'          => 'public-read'
        ));

        // Build absolute path to uploaded resource
        return $this->bucketURL.'/'.(isset($uploadDir{0}) ? $uploadDir . '/' : '');
    }

    /**
     * Check existing current file in current file system
     * @param $url string Url
     * @return boolean File exists or not
     */
    public function exists($url)
    {
        // Get file key name on amazon s3
        $fileKey = str_replace($this->bucketURL, '', $url);
        return $this->client->doesObjectExist($this->bucket, $fileKey);
    }

    /**
     * Read the file from current file system
     * @param $filePath string Full path to file
     * @param $filename string File name
     * @return string File data
     */
    public function read($filePath, $filename = null)
    {
        return file_get_contents($filePath);
    }

    /**
     * Delete file from current file system
     * @param $filePath string File for deleting
     * @return mixed
     */
    public function delete($filePath)
    {
        $this->client->deleteObject(array(
            'Bucket' => $this->bucket,
            'Key'    => str_replace($this->bucketURL, '', $filePath)
        ));
    }

    /**
     * Get file extension in current file system
     * @param $filePath string Path
     * @return string false if extension not found, otherwise file extension
     */
    public function extension($filePath)
    {
        // Get last three symbols of file path
        $extension = substr($filePath, -3);

        // Fix jpeg extension
        if ($extension == 'peg') {
            $extension = 'jpeg';
        }

        return $extension;
    }

    /**
     * Define if $filePath is directory
     * @param string $filePath Path
     * @return boolean Is $path a directory or not
     */
    public function isDir($filePath)
    {
        $isDir = false;
        if ($this->exists($filePath)) {
            $isDir = $this->isKeyDir($filePath);
        }
        return $isDir;
    }

    /**
     * Get recursive $path listing collection
     * @param string    $path       Path for listing contents
     * @param array     $restrict   Collection of restricted paths
     * @param array     $result   Collection of restricted paths
     * @return array    $result     Resulting collection used in recursion
     */
    public function dir($path, $restrict = array(), & $result = array())
    {
        $iterator = $this->client->getIterator('ListObjects', array(
            'Bucket' => $this->bucket,
            'Prefix' => $path
        ));

        foreach ($iterator as $object) {
            $key = $object['Key'];
            if (!$this->isKeyDir($key)) {
                $result[] = $key;
            }
        }

        // Sort results
        if (sizeof($result)) {
            sort($result);
        }

        return $result;
    }

    /**
     * Define if $objectKey is directory
     * @param $objectKey Object Key
     * @return bool Is $objectKey a directory or not
     */
    private function isKeyDir($objectKey)
    {
        $isDir = false;
        $fileKey = preg_replace('/.*'.quotemeta($this->bucket).'\//', '', $objectKey);
        if (($last = substr($fileKey, -1)) === '/') {
            $isDir = true;
        }
        return $isDir;
    }

    /**
     * Create catalog in selected location
     * @param string    $path   Path for new catalog
     * @return boolean  Result of catalog creating
     */
    public function mkDir($path)
    {
        // We cannot create folders directly at AWS
        return true;
    }
}
