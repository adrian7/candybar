<?php
/**
 * PHPUnit Coverage Styles - [file description]
 * @author adrian7
 * @version 1.0
 */

namespace DevLib\Candybar\Transport;

interface FileTransportInterface{

    /**
     * Upload file to destination
     *
     * @param string $path
     * @param string $destination
     *
     * @return mixed
     */
    public function upload($path, $destination);

}