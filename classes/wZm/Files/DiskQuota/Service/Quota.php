<?php

/**
 * Disk quota
 * @author Nikolai Shcherbin
 * @license GNU Public License version 2
 * @copyright (c) Nikolai Shcherbin 2025
 * @link https://wzm.me
**/

namespace wZm\Files\DiskQuota\Service;

class Quota
{
    private $max_allowed_space = 2147483648; // Default: 1 GB

    private $current_user = null;

    private $current_upload_size = 0;

    private $total_size_used = 0;

    public function __construct(\ElggUser $user)
    {
        $this->current_user = $user;

        // current user disk used
        $this->total_size_used = (int) $this->current_user->disk_used;

        // maximun space for the user
        $user_diskquota = 1024 * 1024 * $this->current_user->disk_quota;

        if ($user_diskquota) {
            $this->max_allowed_space = $user_diskquota;
        } else {
            $global_max_allowed_space = (int) elgg_get_plugin_setting('global_disk_space', 'diskquota', 100);

            $this->max_allowed_space = 1024 * 1024 * $global_max_allowed_space;
        }

        // calculate the current upload size
        $this->calculateCurrentUpload();
    }

    /**
     * Calculates the size of the uploaded file
     */
    protected function calculateCurrentUpload()
    {
        $total = 0;

        if (sizeof($_FILES)) {
            foreach ($_FILES as $name => $values) {
                foreach ($values as $key => $value) {
                    if (!is_array($value)) {
                        if ($key == 'error') {
                            $error = $value;
                        }

                        if ($error == 0 && $key == 'size') {
                            $total += $value;
                        }
                    } else {
                        if ($key == 'error') {
                            foreach ($value as $ke => $val) {
                                if ($val == 0) {
                                    $good_keys[] = $ke;
                                }
                            }
                        }

                        if ($key == 'size') {
                            foreach ($good_keys as $keee) {
                                $total += $value[$keee];
                            }
                        }
                    }
                }
            }
        }

        if ($total > 0) {
            $this->current_upload_size = $total;
        }
    }

  /**
   * Size of uploaded file
   * @return int
   */
    public function getCurrentUploadSize(): int
    {
        return (int) $this->current_upload_size;
    }

  /**
   * Validate the uploaded file wheather user has space left or not
   * @return bool
   */
    public function validate(): bool
    {
        if (!$this->getCurrentUploadSize()) {
            return false;
        }

        if (!$this->current_user) {
            return false;
        }

        if (($this->total_size_used + $this->getCurrentUploadSize()) > $this->max_allowed_space) {
            return false;
        }

        $this->current_user->disk_used = $this->total_size_used + $this->getCurrentUploadSize();
        return true;
    }

  /**
   * Update disk space when replacing an existing file
   * @return int
   */
    public function update(int $size = 0): int
    {
        return $this->current_user->disk_used = $this->current_user->disk_used - $size;
    }

  /**
   * Update disk space after deletion file
   * @param ElggFile $entity
   */
    public function refresh(\ElggFile $entity)
    {
        $space_used = (int) $entity->diskspace_used;
        if ($space_used) {
            $this->current_user->disk_used = $this->current_user->disk_used - $space_used;
        }
    }

  /**
   * User quota of space in MB
   * @return float
   */
    public function getDiskquotaMB()
    {
        $space = (float) $this->current_user->disk_quota;
        if (!$space) {
            $space = (float) $this->byteToMb($this->max_allowed_space);
        }

        return $space;
    }

  /**
   * User quota of space in Bytes
   * @return float
   */
    public function getDiskquotaBytes()
    {
        $space = (float) 1024 * 1024 * $this->current_user->disk_quota;
        if (!$space) {
            $space = (float) $this->max_allowed_space;
        }

        return $space;
    }

  /**
   * Used space in MB
   * @return float
   */
    public function getUsedSpaceMB()
    {
        return (float) round($this->byteToMb($this->current_user->disk_used), 2);
    }

  /**
   * Used space in Bytes
   * @return float
   */
    public function getUsedSpaceBytes()
    {
        return (float) $this->current_user->disk_used;
    }

  /**
   * Used space in in %
   * @return float
   */
    public function getUsedSpacePercent()
    {
        $total_used = $this->getUsedSpaceBytes();
        $allowed_space = $this->getDiskquotaBytes();

        return (float) round(($total_used / $allowed_space) * 100, 2);
    }

    public function byteToMb($size)
    {
        return round(((float) $size) / (1024 * 1024), 2);
    }
}
