<?php
/**
 * TeamRPI.php - Team schedule and win/loss for RPI.
 * See "http://derekwilliams.us/?p=4705.
 */

class TeamRPI {

    // Instance:

    private $name, $wins, $losses, $ties, $rpi;
    private $opponents = array();
    private $strengthOfSchedule;

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    public function getWins() { return $this->wins; }
    public function setWins($wins) { $this->wins = $wins; }
    public function getLosses() { return $this->losses; }
    public function setLosses($losses) { $this->losses = $losses; }
    public function getTies() { return $this->ties; }
    public function setTies($ties) { $this->ties = $ties; }
    public function getWinPercent() { return $this->winPercent; }
    public function setWinPercent($percent) { $this->winPercent = $percent; }
    public function getRPI() { return $this->rpi; }
    public function setRPI($rpi) { $this->rpi = $rpi; }

    public function getRecord() {
        return sprintf('%d-%d-%d',
                        $this->getWins(),
                        $this->getLosses(),
                        $this->getTies());
    }
    public function setRecord($record) {
        $words = explode('-', $record);
        $this->setWins($words[0]);
        $this->setLosses($words[1]);
        $this->setTies($words[2]);
        $this->setWinPercent(
            $this->getWins() /
                ($this->getWins() + $this->getLosses()));

    }

    public function getOpponents() { return $this->opponents; }
    public function setOpponents($array) { $this->opponents = $array; }
    public function addOpponent($opponent) {
        array_push($this->opponents, $opponent);
    }

    public function getStrengthOfSchedule() { return $this->strengthOfSchedule; }
    public function setStrengthOfSchedule($sos) { $this->strengthOfSchedule = $sos; }

    public function getOpponentsRecord() { return 0.7; }
    public function getOpponentsOpponentsRecord() { return 0.5; }

    public function compareRPI($team1, $team2) {
        if ($team1->getRPI() == $team2->getRPI())
            return 0;
        return ($team1->getRPI() > $team2->getRPI()) ? -1 : 1;
    }


    // Class (static):

    private static $allTeams = array();
    private static function addTeam($team) { 
        self::$allTeams[$team->getName()] = $team; 
    }

    public static function getAllTeams() { return self::$allTeams; }
    public static function getTeam($name) {
        return self::$allTeams[$name];
    }
    public static function computeAllRPIs() {
        foreach (self::getAllTeams() as $team)
            $team->computeRPI();
    }
    public static function sortByRPI() {
        usort(self::$allTeams, array('TeamRPI', 'compareRPI'));
    }

    /**
     * Compute my Rating Percentage Index (RPI) and Strength of Schedule (SOS).
     * See: http://en.wikipedia.org/wiki/Ratings_Percentage_Index.
     *      http://en.wikipedia.org/wiki/Strength_of_schedule.
     */
    public function computeRPI() {
        foreach ($this->getOpponents() as $opponent) {
            if (!is_null($team = self::getTeam($opponent))) {
                $opponentWins += $team->getWins();
                $opponentGames += $team->getWins() + $team->getLosses();
                foreach ($team->getOpponents() as $opponentOpponent) {
                    if (!is_null($team2 = self::getTeam($opponentOpponent))) {
                        $opponentOpponentWins += $team2->getWins();
                        $opponentOpponentGames += $team2->getWins() + $team2->getLosses();
                    }
                }
            }
        }
        if ($opponentGames > 0)
            $or = $opponentWins / $opponentGames;
        if ($opponentOpponentGames > 0)
            $oor = $opponentOpponentWins / $opponentOpponentGames;
        $this->setStrengthOfSchedule(((2*$or) + $oor)/3);

        $this->setRPI((0.25 * $this->getWinPercent())
                            + (0.5 * $or) + (0.25 * $oor));
    }

    /**
     * Parse schedules and results and build my static list of teams.
     * Ignore comment lines (#) and blank lines.  Parse lines in this format:
     *  : Auburn	14-0-0 	W-15
     *      - 09/04/10	+	Arkansas St.	52	26
     * Sample: http://derekwilliams.us/fragments/ncaaf-2010.txt.
     */
    public static function parseSchedules($schedules) {
        foreach (explode("\n", $schedules) as $line) {
          $words = preg_split("/[\t]+/", $line);
          if (count($words) > 3 && !ereg('^#', $line)) {
              if (ereg('^:', $line)) {
                  $currentTeam = new TeamRPI();
                  $currentTeam->setName($words[1]);
                  $currentTeam->setRecord($words[2]);
                  self::addTeam($currentTeam);
              } elseif ($words[1] == '-' && !is_null($currentTeam))
                  $currentTeam->addOpponent($words[4]);
          }
        }
    }
}
?>