<?php
/**
 * ELM-LSApp Enrollment Synchronization
 * 
 * This script synchronizes enrollment data between ELM and LSApp systems.
 * It reads the output of GBC_CURRENT_COURSE_INFO ELM query, matches ITEM codes
 * with LSApp records, and updates LSApp with current ELM status and attendance numbers.
 */

require('inc/lsapp.php');

class ElmSyncProcessor {
    private $elmFilePath = 'data/elm.csv';
    private $classesFilePath = 'data/classes.csv';
    private $coursesFilePath = 'data/courses.csv';
    private $backupDir = 'data/backups/';
    
    private $elmHeaders;
    private $lsappHeaders;
    private $lsappClasses = [];
    private $updatedCount = 0;
    private $updatedClasses = [];
    
    /**
     * Initialize the synchronization process
     */
    public function initialize() {
        if (!$this->createBackups()) {
            return false;
        }
        
        $this->loadLsappClasses();
        return true;
    }
    
    /**
     * Create backup files before processing
     * 
     * @return bool True if backups were successful, false otherwise
     */
    private function createBackups() {
        $timestamp = date('Ymd\THis');
        
        $classesBackupFile = $this->backupDir . 'classes' . $timestamp . '.csv';
        if (!copy($this->classesFilePath, $classesBackupFile)) {
            echo "Failed to backup $classesBackupFile...\nPlease inform the Team Lead ASAP";
            return false;
        }
        
        $coursesBackupFile = $this->backupDir . 'courses' . $timestamp . '.csv';
        if (!copy($this->coursesFilePath, $coursesBackupFile)) {
            echo "Failed to backup $coursesBackupFile...\nPlease inform the Team Lead ASAP";
            return false;
        }
        
        return true;
    }
    
    /**
     * Load LSApp classes from CSV file
     */
    private function loadLsappClasses() {
        $lsapp = fopen($this->classesFilePath, 'r');
        $this->lsappHeaders = fgetcsv($lsapp);
        
        while ($row = fgetcsv($lsapp)) {
            $this->lsappClasses[] = $row;
        }
        
        fclose($lsapp);
    }
    
    /**
     * Process the ELM data and update LSApp classes
     * 
     * @return array Array of updated classes
     */
    public function processElmData() {
        $elm = fopen($this->elmFilePath, 'r');
        $this->elmHeaders = fgetcsv($elm);
        
        while ($elmRow = fgetcsv($elm)) {
            $this->processElmRow($elmRow);
        }
        
        fclose($elm);
        
        // Write updated data back to classes.csv
        $this->saveLsappClasses();
        
        return $this->updatedClasses;
    }
    
    /**
     * Process a single ELM row and update matching LSApp class
     * 
     * @param array $elmRow Row data from ELM CSV
     */
    private function processElmRow($elmRow) {
        foreach ($this->lsappClasses as $key => $lsappClass) {
            if ($elmRow[1] == $lsappClass[7]) {
                $newEnrolled = intval($elmRow[8]) + intval($elmRow[16]);
                
                // Check if there's a difference between ELM and LSApp
                if ($this->needsUpdate($newEnrolled, $elmRow, $lsappClass)) {
                    $this->updatedCount++;
                    
                    // Update the class data
                    $this->lsappClasses[$key][1] = $elmRow[5]; // status
                    $this->lsappClasses[$key][18] = $newEnrolled; // enrolled + in-progress
                    $this->lsappClasses[$key][19] = $elmRow[9]; // Reserved
                    $this->lsappClasses[$key][20] = $elmRow[10]; // Pending
                    $this->lsappClasses[$key][21] = $elmRow[11]; // Waitlist
                    $this->lsappClasses[$key][22] = $elmRow[12]; // Dropped
                    
                    // Store updated class info for display
                    $this->updatedClasses[] = [
                        'classId' => $lsappClass[0],
                        'name' => $lsappClass[6],
                        'date' => [$lsappClass[8], $lsappClass[9]],
                        'itemCode' => $lsappClass[7],
                        'elm' => [
                            'enrolled' => $newEnrolled,
                            'reserved' => $elmRow[9],
                            'pending' => $elmRow[10],
                            'waitlist' => $elmRow[11],
                            'dropped' => $elmRow[12]
                        ],
                        'lsapp' => [
                            'enrolled' => $lsappClass[18],
                            'reserved' => $lsappClass[19],
                            'pending' => $lsappClass[20],
                            'waitlist' => $lsappClass[21],
                            'dropped' => $lsappClass[22]
                        ]
                    ];
                }
                
                break; // Found matching class, no need to continue
            }
        }
    }
    
    /**
     * Check if a class needs to be updated
     * 
     * @param int $newEnrolled New enrollment count
     * @param array $elmRow ELM data
     * @param array $lsappClass LSApp class data
     * @return bool True if update is needed
     */
    private function needsUpdate($newEnrolled, $elmRow, $lsappClass) {
        return $newEnrolled != $lsappClass[18] || 
               $elmRow[9] != $lsappClass[19] || 
               $elmRow[10] != $lsappClass[20] || 
               $elmRow[11] != $lsappClass[21] || 
               $elmRow[12] != $lsappClass[22];
    }
    
    /**
     * Save updated LSApp classes back to CSV
     */
    private function saveLsappClasses() {
        $newClasses = fopen($this->classesFilePath, 'w');
        
        // Add the headers
        fputcsv($newClasses, $this->lsappHeaders);
        
        // Write all classes
        foreach ($this->lsappClasses as $fields) {
            fputcsv($newClasses, $fields);
        }
        
        fclose($newClasses);
    }
    
    /**
     * Get the count of updated classes
     * 
     * @return int Count of updated classes
     */
    public function getUpdatedCount() {
        return $this->updatedCount;
    }
    
    /**
     * Get the list of updated classes
     * 
     * @return array Updated classes data
     */
    public function getUpdatedClasses() {
        return $this->updatedClasses;
    }
}

/**
 * UI Helper class to handle display functions
 */
class SyncUI {
    /**
     * Render the updated class list
     * 
     * @param array $updatedClasses List of updated classes
     * @return string HTML output
     */
    public function renderUpdatedClassList($updatedClasses) {
        if (empty($updatedClasses)) {
            return '<div class="alert alert-info">No classes needed updating.</div>';
        }
        
        $output = '<ul class="list-group">';
        
        foreach ($updatedClasses as $class) {
            $output .= $this->renderClassItem($class);
        }
        
        $output .= '</ul>';
        return $output;
    }
    
    /**
     * Render a single class item
     * 
     * @param array $class Class data
     * @return string HTML output
     */
    private function renderClassItem($class) {
        $output = '<li class="list-group-item">';
        $output .= '<a href="class.php?classid=' . $class['classId'] . '">';
        $output .= '<strong>' . $class['name'] . '</strong><br>';
        $output .= goodDateLong($class['date'][0], $class['date'][1]) . '<br>';
        $output .= $class['itemCode'] . ' UPDATED.';
        $output .= '</a>';
        $output .= '<div class="alert alert-warning">';
        
        // Enrolled
        $output .= 'ELM Enrolled/In-Progress: ' . $class['elm']['enrolled'] . ' | ';
        if ($class['elm']['enrolled'] != $class['lsapp']['enrolled']) {
            $output .= '<strong>LSApp Enrolled: ' . $class['lsapp']['enrolled'] . '</strong><br>';
        } else {
            $output .= 'LSApp Enrolled: ' . $class['lsapp']['enrolled'] . '<br>';
        }
        
        // Reserved
        $output .= 'ELM Reserved: ' . $class['elm']['reserved'] . ' | ';
        if ($class['elm']['reserved'] != $class['lsapp']['reserved']) {
            $output .= '<strong>LSApp Reserved: ' . $class['lsapp']['reserved'] . '</strong><br>';
        } else {
            $output .= 'LSApp Reserved: ' . $class['lsapp']['reserved'] . '<br>';
        }
        
        // Pending
        $output .= 'ELM Pending: ' . $class['elm']['pending'] . ' | ';
        if ($class['elm']['pending'] != $class['lsapp']['pending']) {
            $output .= '<strong>LSApp Pending: ' . $class['lsapp']['pending'] . '</strong><br>';
        } else {
            $output .= 'LSApp Pending: ' . $class['lsapp']['pending'] . '<br>';
        }
        
        // Waitlist
        $output .= 'ELM Waitlist: ' . $class['elm']['waitlist'] . ' | ';
        if ($class['elm']['waitlist'] != $class['lsapp']['waitlist']) {
            $output .= '<strong>LSApp Waitlist: ' . $class['lsapp']['waitlist'] . '</strong><br>';
        } else {
            $output .= 'LSApp Waitlist: ' . $class['lsapp']['waitlist'] . '<br>';
        }
        
        // Dropped
        $output .= 'ELM Dropped: ' . $class['elm']['dropped'] . ' | ';
        if ($class['elm']['dropped'] != $class['lsapp']['dropped']) {
            $output .= '<strong>LSApp Dropped: ' . $class['lsapp']['dropped'] . '</strong><br>';
        } else {
            $output .= 'LSApp Dropped: ' . $class['lsapp']['dropped'] . '<br>';
        }
        
        $output .= '</div>';
        $output .= '</li>';
        
        return $output;
    }
}

// Main execution
$processor = new ElmSyncProcessor();
$syncUI = new SyncUI();

// Only process if initialization is successful
$initSuccess = $processor->initialize();
$updatedClasses = [];

if ($initSuccess) {
    $updatedClasses = $processor->processElmData();
}
?>

<?php getHeader() ?>

<title>Upload PUBLIC.GBC_CURRENT_COURSE_INFO</title>

<style>
.upcount {
    font-size: 30px;
    margin: 30px 0;
}
</style>
<?php getScripts() ?>

<?php getNavigation() ?>

<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-12">
<?php include('templates/admin-nav.php') ?>
</div>
<div class="col-md-6">
<h2>ELM - LSApp Enrolment Number Synchronize</h2>
<?php if ($initSuccess): ?>
    <div class="alert alert-success">Synchronization Completed</div>
    <?php if(isAdmin()): ?>
        <?php echo $syncUI->renderUpdatedClassList($updatedClasses); ?>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-danger">Synchronization Failed - Backup Error</div>
<?php endif; ?>
</div>
<div class="col-md-4">
<h2><span class="badge text-bg-dark"><?= $processor->getUpdatedCount() ?></span> Updated.</h2>
</div>
</div>
</div>

<?php require('templates/javascript.php') ?>

<?php require('templates/footer.php') ?>