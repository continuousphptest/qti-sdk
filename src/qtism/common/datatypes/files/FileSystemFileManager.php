<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @license GPLv2
 */

namespace qtism\common\datatypes\files;

use qtism\common\datatypes\File;
use \RuntimeException;

/**
 * This implementation of FileManager is the default one of QTISM.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class FileSystemFileManager implements FileManager
{
    private $storageDirectory;

    public function __construct($storageDirectory = '')
    {
        $this->setStorageDirectory((empty($storageDirectory) === true) ? sys_get_temp_dir() : $storageDirectory);
    }

    /**
     * Set the canonical path where files will be effectively stored
     * on the file system.
     *
     * @param string $storageDirectory A canonical path.
     */
    protected function setStorageDirectory($storageDirectory)
    {
        $this->storageDirectory = $storageDirectory;
    }

    /**
     * Get the canonical path where files will be effectively stored
     * on the file system.
     *
     * @return string A canonical path.
     */
    protected function getStorageDirectory()
    {
        return $this->storageDirectory;
    }

    /**
     * Create a FileSystemFile object from an existing
     * file on the file system.
     *
     * @param string $path The canonical path to the file.
     * @param string $mimeType The mime-type of the file (if you want to force it).
     * @param string $filename The file name of the file (if you want to force it).
     * @throws \qtism\common\datatypes\files\FileManagerException
     * @return \qtism\common\datatypes\files\FileSystemFile
     */
    public function createFromFile($path, $mimeType, $filename = '')
    {
        $destination = $this->buildDestination();

        try {
            return FileSystemFile::createFromExistingFile($path, $destination, $mimeType, $filename);
        } catch (RuntimeException $e) {
            $msg = "An error occured while creating a QTI FileSystemFile object.";
            throw new FileManagerException($msg, 0, $e);
        }
    }

    /**
     * Create a FileSystemFile from existing data on the file system.
     *
     * @param string $data The binary data of the FileSystemFile object to be created.
     * @param string $mimeType A mime-type.
     * @param string $filename A file name e.g. "myfile.txt".
     * @throws \qtism\common\datatypes\files\FileManagerException
     * @return \qtism\common\datatypes\files\FileSystemFile
     */
    public function createFromData($data, $mimeType, $filename = '')
    {
        $destination = $this->buildDestination();

        try {
            return FileSystemFile::createFromData($data, $destination, $mimeType, $filename);
        } catch (RuntimeException $e) {
            $msg = "An error occured while creating a QTI FileSystemFile object.";
            throw new FileManagerException($msg, 0, $e);
        }
    }

    /**
     * Retrieve a FileSystemFile object from its unique identifier.
     *
     * @param string identifier
     * @throws \qtism\common\datatypes\files\FileManagerException
     * @return \qtism\common\datatypes\files\FileSystemFile
     */
    public function retrieve($identifier)
    {
        try {
            return FileSystemFile::retrieveFile($identifier);
        } catch (RuntimeException $e) {
            $msg = "An error occured while retrieving a QTI FileSystemFile object.";
            throw new FileManagerException($msg, 0, $e);
        }
    }

    /**
     * Delete a FileSystemFile object from the persistence.
     *
     * @throws \qtism\common\datatypes\files\FileManagerException
     */
    public function delete(File $file)
    {
        $deletion = @unlink($file->getPath());
        if ($deletion === false) {
            $msg = "The File System File located at '" . $file->getPath() . "' could not be deleted gracefully.";
            throw new FileManagerException($msg);
        }
    }

    /**
     * Build the destination directory where file will be stored.
     *
     * @return string
     */
    protected function buildDestination()
    {
        return rtrim($this->getStorageDirectory(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . uniqid('qtism', true);
    }
}
