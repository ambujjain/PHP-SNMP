<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib;

/**
 * See RFC 2790 https://tools.ietf.org/html/rfc2790
 */
class HostResources extends MibBase
{
    public const OID_HOST                              = '.1.3.6.1.2.1.25';
    public const OID_HR_SYSTEM                         = '.1.3.6.1.2.1.25.1';
    public const OID_HR_SYSTEM_UPTIME                  = '.1.3.6.1.2.1.25.1.1';
    public const OID_HR_SYSTEM_DATE                    = '.1.3.6.1.2.1.25.1.2';
    public const OID_HR_SYSTEM_INITIAL_LOAD_DEVICE     = '.1.3.6.1.2.1.25.1.3';
    public const OID_HR_SYSTEM_INITIAL_LOAD_PARAMETERS = '.1.3.6.1.2.1.25.1.4';
    public const OID_HR_SYSTEM_NUM_USERS               = '.1.3.6.1.2.1.25.1.5';
    public const OID_HR_SYSTEM_PROCESSES               = '.1.3.6.1.2.1.25.1.6';
    public const OID_HR_SYSTEM_MAX_PROCESSES           = '.1.3.6.1.2.1.25.1.7';
    public const OID_HR_STORAGE                        = '.1.3.6.1.2.1.25.2';
    public const OID_HR_STORAGE_TYPES                  = '.1.3.6.1.2.1.25.2.1';
    public const OID_HR_MEMORY_SIZE                    = '.1.3.6.1.2.1.25.2.2';
    public const OID_HR_STORAGE_TABLE                  = '.1.3.6.1.2.1.25.2.3';
    public const OID_HR_STORAGE_ENTRY                  = '.1.3.6.1.2.1.25.2.3.1';
    public const OID_HR_STORAGE_INDEX                  = '.1.3.6.1.2.1.25.2.3.1.1';
    public const OID_HR_STORAGE_TYPE                   = '.1.3.6.1.2.1.25.2.3.1.2';
    public const OID_HR_STORAGE_DESCR                  = '.1.3.6.1.2.1.25.2.3.1.3';
    public const OID_HR_STORAGE_ALLOCATION_UNITS       = '.1.3.6.1.2.1.25.2.3.1.4';
    public const OID_HR_STORAGE_SIZE                   = '.1.3.6.1.2.1.25.2.3.1.5';
    public const OID_HR_STORAGE_USED                   = '.1.3.6.1.2.1.25.2.3.1.6';
    public const OID_HR_STORAGE_ALLOCATION_FAILURES    = '.1.3.6.1.2.1.25.2.3.1.7';
    public const OID_HR_DEVICE                         = '.1.3.6.1.2.1.25.3';
    public const OID_HR_DEVICE_TYPES                   = '.1.3.6.1.2.1.25.3.1';
    public const OID_HR_DEVICE_TABLE                   = '.1.3.6.1.2.1.25.3.2';
    public const OID_HR_DEVICE_ENTRY                   = '.1.3.6.1.2.1.25.3.2.1';
    public const OID_HR_DEVICE_INDEX                   = '.1.3.6.1.2.1.25.3.2.1.1';
    public const OID_HR_DEVICE_TYPE                    = '.1.3.6.1.2.1.25.3.2.1.2';
    public const OID_HR_DEVICE_DESCR                   = '.1.3.6.1.2.1.25.3.2.1.3';
    public const OID_HR_DEVICE_ID                      = '.1.3.6.1.2.1.25.3.2.1.4';
    public const OID_HR_DEVICE_STATUS                  = '.1.3.6.1.2.1.25.3.2.1.5';
    public const OID_HR_DEVICE_ERRORS                  = '.1.3.6.1.2.1.25.3.2.1.6';
    public const OID_HR_PROCESSOR_TABLE                = '.1.3.6.1.2.1.25.3.3';
    public const OID_HR_PROCESSOR_ENTRY                = '.1.3.6.1.2.1.25.3.3.1';
    public const OID_HR_PROCESSOR_FRW_ID               = '.1.3.6.1.2.1.25.3.3.1.1';
    public const OID_HR_PROCESSOR_LOAD                 = '.1.3.6.1.2.1.25.3.3.1.2';
    public const OID_HR_NETWORK_TABLE                  = '.1.3.6.1.2.1.25.3.4';
    public const OID_HR_NETWORK_ENTRY                  = '.1.3.6.1.2.1.25.3.4.1';
    public const OID_HR_NETWORK_IF_INDEX               = '.1.3.6.1.2.1.25.3.4.1.1';
    public const OID_HR_PRINTER_TABLE                  = '.1.3.6.1.2.1.25.3.5';
    public const OID_HR_PRINTER_ENTRY                  = '.1.3.6.1.2.1.25.3.5.1';
    public const OID_HR_PRINTER_STATUS                 = '.1.3.6.1.2.1.25.3.5.1.1';
    public const OID_HR_PRINTER_DETECTED_ERROR_STATE   = '.1.3.6.1.2.1.25.3.5.1.2';
    public const OID_HR_DISK_STORAGE_TABLE             = '.1.3.6.1.2.1.25.3.6';
    public const OID_HR_DISK_STORAGE_ENTRY             = '.1.3.6.1.2.1.25.3.6.1';
    public const OID_HR_DISK_STORAGE_ACCESS            = '.1.3.6.1.2.1.25.3.6.1.1';
    public const OID_HR_DISK_STORAGE_MEDIA             = '.1.3.6.1.2.1.25.3.6.1.2';
    public const OID_HR_DISK_STORAGE_REMOVEBLE         = '.1.3.6.1.2.1.25.3.6.1.3';
    public const OID_HR_DISK_STORAGE_CAPACITY          = '.1.3.6.1.2.1.25.3.6.1.4';
    public const OID_HR_PARTITION_TABLE                = '.1.3.6.1.2.1.25.3.7';
    public const OID_HR_PARTITION_ENTRY                = '.1.3.6.1.2.1.25.3.7.1';
    public const OID_HR_PARTITION_INDEX                = '.1.3.6.1.2.1.25.3.7.1.1';
    public const OID_HR_PARTITION_LABEL                = '.1.3.6.1.2.1.25.3.7.1.2';
    public const OID_HR_PARTITION_ID                   = '.1.3.6.1.2.1.25.3.7.1.3';
    public const OID_HR_PARTITION_SIZE                 = '.1.3.6.1.2.1.25.3.7.1.4';
    public const OID_HR_PARTITION_FSINDEX              = '.1.3.6.1.2.1.25.3.7.1.5';
    public const OID_HR_FSTABLE                        = '.1.3.6.1.2.1.25.3.8';
    public const OID_HR_FSENTRY                        = '.1.3.6.1.2.1.25.3.8.1';
    public const OID_HR_FSINDEX                        = '.1.3.6.1.2.1.25.3.8.1.1';
    public const OID_HR_FSMOUNT_POINT                  = '.1.3.6.1.2.1.25.3.8.1.2';
    public const OID_HR_FSREMOTE_MOUNT_POINT           = '.1.3.6.1.2.1.25.3.8.1.3';
    public const OID_HR_FSTYPE                         = '.1.3.6.1.2.1.25.3.8.1.4';
    public const OID_HR_FSACCESS                       = '.1.3.6.1.2.1.25.3.8.1.5';
    public const OID_HR_FSBOOTABLE                     = '.1.3.6.1.2.1.25.3.8.1.6';
    public const OID_HR_FSSTORAGE_INDEX                = '.1.3.6.1.2.1.25.3.8.1.7';
    public const OID_HR_FSLAST_FULL_BACKUP_DATE        = '.1.3.6.1.2.1.25.3.8.1.8';
    public const OID_HR_FSLAST_PARTIAL_BACKUP_DATE     = '.1.3.6.1.2.1.25.3.8.1.9';
    public const OID_HR_FSTYPES                        = '.1.3.6.1.2.1.25.3.9';
    public const OID_HR_SWRUN                          = '.1.3.6.1.2.1.25.4';
    public const OID_HR_SWOSINDEX                      = '.1.3.6.1.2.1.25.4.1';
    public const OID_HR_SWRUN_TABLE                    = '.1.3.6.1.2.1.25.4.2';
    public const OID_HR_SWRUN_ENTRY                    = '.1.3.6.1.2.1.25.4.2.1';
    public const OID_HR_SWRUN_INDEX                    = '.1.3.6.1.2.1.25.4.2.1.1';
    public const OID_HR_SWRUN_NAME                     = '.1.3.6.1.2.1.25.4.2.1.2';
    public const OID_HR_SWRUN_ID                       = '.1.3.6.1.2.1.25.4.2.1.3';
    public const OID_HR_SWRUN_PATH                     = '.1.3.6.1.2.1.25.4.2.1.4';
    public const OID_HR_SWRUN_PARAMETERS               = '.1.3.6.1.2.1.25.4.2.1.5';
    public const OID_HR_SWRUN_TYPE                     = '.1.3.6.1.2.1.25.4.2.1.6';
    public const OID_HR_SWRUN_STATUS                   = '.1.3.6.1.2.1.25.4.2.1.7';
    public const OID_HR_SWRUN_PERF                     = '.1.3.6.1.2.1.25.5';
    public const OID_HR_SWRUN_PERF_TABLE               = '.1.3.6.1.2.1.25.5.1';
    public const OID_HR_SWRUN_PERF_ENTRY               = '.1.3.6.1.2.1.25.5.1.1';
    public const OID_HR_SWRUN_PERF_CPU                 = '.1.3.6.1.2.1.25.5.1.1.1';
    public const OID_HR_SWRUN_PERF_MEM                 = '.1.3.6.1.2.1.25.5.1.1.2';
    public const OID_HR_SWINSTALLED                    = '.1.3.6.1.2.1.25.6';
    public const OID_HR_SWINSTALLED_LAST_CHANGE        = '.1.3.6.1.2.1.25.6.1';
    public const OID_HR_SWINSTALLED_LAST_UPDATE_TIME   = '.1.3.6.1.2.1.25.6.2';
    public const OID_HR_SWINSTALLED_TABLE              = '.1.3.6.1.2.1.25.6.3';
    public const OID_HR_SWINSTALLED_ENTRY              = '.1.3.6.1.2.1.25.6.3.1';
    public const OID_HR_SWINSTALLED_INDEX              = '.1.3.6.1.2.1.25.6.3.1.1';
    public const OID_HR_SWINSTALLED_NAME               = '.1.3.6.1.2.1.25.6.3.1.2';
    public const OID_HR_SWINSTALLED_ID                 = '.1.3.6.1.2.1.25.6.3.1.3';
    public const OID_HR_SWINSTALLED_TYPE               = '.1.3.6.1.2.1.25.6.3.1.4';
    public const OID_HR_SWINSTALLED_DATE               = '.1.3.6.1.2.1.25.6.3.1.5';
    public const OID_HR_MIBADMIN_INFO                  = '.1.3.6.1.2.1.25.7';
    public const OID_HOST_RESOURCES_MIB_MODULE         = '.1.3.6.1.2.1.25.7.1';
    public const OID_HR_MIBCOMPLIANCES                 = '.1.3.6.1.2.1.25.7.2';
    public const OID_HR_MIBCOMPLIANCE                  = '.1.3.6.1.2.1.25.7.2.1';
    public const OID_HR_MIBGROUPS                      = '.1.3.6.1.2.1.25.7.3';
    public const OID_HR_SYSTEM_GROUP                   = '.1.3.6.1.2.1.25.7.3.1';
    public const OID_HR_STORAGE_GROUP                  = '.1.3.6.1.2.1.25.7.3.2';
    public const OID_HR_DEVICE_GROUP                   = '.1.3.6.1.2.1.25.7.3.3';
    public const OID_HR_SWRUN_GROUP                    = '.1.3.6.1.2.1.25.7.3.4';
    public const OID_HR_SWRUN_PERF_GROUP               = '.1.3.6.1.2.1.25.7.3.5';
    public const OID_HR_SWINSTALLED_GROUP              = '.1.3.6.1.2.1.25.7.3.6';

    /** @return int[] */
    public function getHost() : array
    {
        return $this->getSnmp()->walk(self::OID_HOST);
    }

    /** @return string[] */
    public function getHrStorageType() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_HR_STORAGE_TYPE);
    }

    /** @return string[] */
    public function getHrStorageDescr() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_HR_STORAGE_DESCR);
    }

    /** @return int[] */
    public function getHrStorageSize() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_HR_STORAGE_SIZE);
    }

    /** @return int[] */
    public function getHrStorageUsed() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_HR_STORAGE_USED);
    }

    /** @return int[] */
    public function getHrDeviceType() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_HR_DEVICE_TYPE);
    }

    /** @return string[] */
    public function getHrDeviceDescr() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_HR_DEVICE_DESCR);
    }

    /** @return int[] */
    public function getHrDeviceStatus() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_HR_DEVICE_STATUS);
    }

    /** @return int[] */
    public function getHrProcessorLoad() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_HR_PROCESSOR_LOAD);
    }
}
