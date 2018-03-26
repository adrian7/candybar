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
     * @param string $filename
     * @param string $destination
     *
     * @return mixed
     */
    public function upload($filename, $destination);

}