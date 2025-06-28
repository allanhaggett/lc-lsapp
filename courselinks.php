<?php 
require('inc/lsapp.php');
opcache_reset();

if(!canAccess()) {
    header('Location: /lsapp/');
    exit;
}

// Read courses from CSV
$courses = [];
if (($handle = fopen("data/courses.csv", "r")) !== FALSE) {
    // Skip header row
    $headers = fgetcsv($handle);
    
    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        // Only include courses with HUBInclude set to 'yes' or '1'
        $hubInclude = $data[53] ?? '';
        if ($hubInclude === 'Yes' || $hubInclude === 'yes' || $hubInclude === '1') {
            $courses[] = [
                'id' => $data[0] ?? '',
                'name' => $data[2] ?? '',
                'platform' => $data[52] ?? '',
                'elearning' => $data[22] ?? '',
                'registrationLink' => $data[54] ?? ''
            ];
        }
    }
    fclose($handle);
}

?>
<?php getHeader() ?>
<title>Learning Hub Course Links - LSApp</title>
<style>
.sort {
    cursor: pointer;
    position: relative;
    padding-right: 20px;
}
.sort:after {
    content: "↕";
    position: absolute;
    right: 5px;
    opacity: 0.5;
}
.sort.asc:after {
    content: "↑";
    opacity: 1;
}
.sort.desc:after {
    content: "↓";
    opacity: 1;
}
.search-highlight {
    background-color: #fff3cd;
}
/* Sticky table headers */
.table-container {
    max-height: calc(100vh - 250px);
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    border: 1px solid #dee2e6;
}
.table {
    margin-bottom: 0;
}
.table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #212529;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}
</style>
<?php getScripts() ?>
<body>
<?php getNavigation() ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="my-4">Learning Hub Course Links</h1>
            <p class="text-muted">Showing only courses included in the Learning Hub (HUBInclude = Yes)</p>
            
            <div class="mb-3">
                <input type="text" class="form-control search" placeholder="Search courses..." style="max-width: 400px;">
            </div>
            
            <div id="courseTable">
                <div class="table-container">
                    <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th class="sort" data-sort="name">Course Name</th>
                            <th class="sort" data-sort="platform">Platform</th>
                            <th class="sort" data-sort="elearning">eLearning URL</th>
                            <th class="sort" data-sort="registration">Registration Link</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        <?php foreach($courses as $course): ?>
                        <tr>
                            <td class="name">
                                <a href="course.php?courseid=<?= urlencode($course['id']) ?>">
                                    <?= htmlspecialchars($course['name']) ?>
                                </a>
                            </td>
                            <td class="platform"><?= htmlspecialchars($course['platform']) ?></td>
                            <td class="elearning">
                                <?php if(!empty($course['elearning'])): ?>
                                    <a href="<?= htmlspecialchars($course['elearning']) ?>" target="_blank" class="text-truncate d-inline-block" style="max-width: 300px;">
                                        <?= htmlspecialchars($course['elearning']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif ?>
                            </td>
                            <td class="registration">
                                <?php if(!empty($course['registrationLink'])): ?>
                                    <a href="<?= htmlspecialchars($course['registrationLink']) ?>" target="_blank" class="text-truncate d-inline-block" style="max-width: 300px;">
                                        <?= htmlspecialchars($course['registrationLink']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif ?>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                </div>
                
                <?php if(empty($courses)): ?>
                <div class="alert alert-info">No courses found.</div>
                <?php endif ?>
            </div>
            
            <div class="mt-3 text-muted">
                Total courses: <span class="badge bg-secondary"><?= count($courses) ?></span>
            </div>
        </div>
    </div>
</div>

<script src="js/list.min.js"></script>
<script>
// Initialize List.js with search and sort functionality
var options = {
    valueNames: ['name', 'platform', 'elearning', 'registration'],
    searchClass: 'search'
};

var courseList = new List('courseTable', options);

// Add visual feedback for sorting
courseList.on('sortComplete', function() {
    // Remove all sort indicators
    document.querySelectorAll('.sort').forEach(function(el) {
        el.classList.remove('asc', 'desc');
    });
    
    // Add appropriate sort indicator
    var sortedBy = courseList.sortColumn.els[0];
    if(sortedBy) {
        sortedBy.classList.add(courseList.sortColumn.order);
    }
});

// Highlight search terms
document.querySelector('.search').addEventListener('input', function(e) {
    var searchTerm = e.target.value.toLowerCase();
    
    // Remove existing highlights
    document.querySelectorAll('.search-highlight').forEach(function(el) {
        el.classList.remove('search-highlight');
    });
    
    // Add highlights if there's a search term
    if(searchTerm.length > 0) {
        document.querySelectorAll('.list td').forEach(function(td) {
            if(td.textContent.toLowerCase().includes(searchTerm)) {
                td.classList.add('search-highlight');
            }
        });
    }
});
</script>

<?php require('templates/footer.php') ?>
</body>
</html>