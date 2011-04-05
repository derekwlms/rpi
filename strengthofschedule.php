<?php
    require 'Team.php';
    if (isset($_POST['submit'])) {
        calculateStrengthOfSchedule($_POST['schedules']);
    }

    function calculateStrengthOfSchedule($schedules) {
        Team::parseSchedules($schedules);
        Team::computeAllScheduleStrengths();        
        show_teams(Team::getAllTeams());
    }

    function show_teams($teams) {
        echo '<table border="1">';
        echo '<th>Name</th><th>Record</th><th>Win %</th><th>SOS</th>';
        $i=1;
        foreach ($teams as $team) {
            echo '<tr border="1">';
            printf('<td border="1">%s</td>',$team->getName());
            printf('<td border="1">%s</td>',$team->getRecord());
            printf('<td border="1">%.3f</td>',$team->getWinPercent());
            printf('<td border="1">%.3f</td>',$team->getStrengthOfSchedule());
            echo '</tr>';
        }
        echo '</table>';
    }
?>
