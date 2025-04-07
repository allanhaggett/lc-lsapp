<div class="p-3 rounded-3 bg-dark-subtle">
    <div class="mb-2">
        <a target="_blank"
            rel="nooperner"
            href="https://bcgov-my.sharepoint.com/:u:/r/personal/leah_hassall_gov_bc_ca/_layouts/15/Doc.aspx?sourcedoc=%7B0C19027C-EE0F-439A-9ABB-F3AE55B2A7EA%7D&file=course-revision-process-map-by-role.vsdx&action=default&mobileredirect=true" 
            class="btn btn-secondary">
                Process documentation
        </a>
    </div>
    <?php $thisdoer = ''; if($formData['assign_to'] == LOGGED_IN_IDIR) $thisdoer = 'open'; ?>
    <details class="mb-2" <?= $thisdoer ?>>
        <summary class="mb-2"><?= $cat ?> guidance</summary>
        <div class="p-2 rounded-3 bg-light-subtle">
        <?= $Parsedown->text($guidance) ?>
        </div>
    </details>
    <details id="scopeguide">
    <summary class="mb-2">Scope guidance</summary>
        <div class="mb-2 p-2 bg-light-subtle rounded-2">
            <h3>Minor Change</h3>
            <div><strong>1-2 hours </strong></div>
            <p>Small revisions to existing content that don’t significantly change the 
                meaning/consultation with the business owner is not required (e.g., typos, 
                updating links to existing or new versions of small assets (e.g., images), 
                minor big fixes that don’t significantly alter the user experience, changes 
                that don’t require extensive testing, small adjustments to quiz questions 
                in Moodle or HTML).</p>
        </div>
        <div class="mb-2 p-2 bg-light-subtle rounded-2">
            <h3>Moderate </h3>
            <div><strong>2 hours – 24 hours </strong></div>
            <p>Moderate changes to content (needing business owner approval), updating or 
                reorganizing content in multiple lessons or modules, adding/updating evaluation 
                surveys, adjustments to quizzes built in Storyline, updating videos/interactive 
                activities, adding new activities/quizzes, multiple changes from an annual 
                review, or changes that require more than one person (e.g., developer). </p>
        </div>
        <div class="mb-2 p-2 bg-light-subtle rounded-2">
            <h3>Major</h3>
            <div><strong>> 24 hours </strong></div>
            <p>Course overhauls or complete reorganization of existing content, revising learning 
                objectives, creating videos, simulations, requires extensive consultation with 
                business owners.</p>
        </div>   
    
    </details>
    <details id="approvalguide">
    <summary class="mb-2">Approval guidance</summary>
    <dl>
        <dt>Pending Approval</dt>
        <dt>Denied</dt>
        <dt>On Hold</dt>
        <dt>Approved</dt>
    </dl>
    </details>
    <details id="progressguide">
    <summary class="mb-2">Progress guidance</summary>
    <dl>
        <dt>Not Started</dt>
        <dt>In Progress</dt>
        <dt>In Review</dt>
        <dt>Ready to Publish</dt>
        <dt>Closed</dt>
    </dl>
    </details>

</div>