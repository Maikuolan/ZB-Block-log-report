<?php
// open log file and setup storage
// following is an include containing the declarations to access your MYSQL DB
// you need to set this accordingly
// the file info parameters must be contained in the variable $db
include 'xxx/yyy.inc';
$filename = 'zbblock/vault/killed_log.csv';

if (!file_exists($filename)) {
    exit("$filename Does not Exist");
    // what about touch()?
}

$csvFile  = file($filename);
$data     = array();
$count    = 0;
$last     = 0;
$totals   = array();
$firstday = 0;
$firstbot = 0;
$checktot = 0;
$botsCount= 0;

print('<html><body>');
print('<table><tr style="text-align: center"><td>Time</td><td>Record</td><td style="width: 100px">Query</td><td>URL</td><td>Why Blocked</td><td style="width: 200px">Referral</td></tr>');
// how far did we get last time
$result = mysqli_query($db, "SELECT * FROM zbblock
			WHERE type = 5
                       ");
if ($row = mysqli_fetch_array($result)) {
    $lastrecord = $row["total"];
    $email      = $row["why"];
}
// store blocks we are not interested in 
$result = mysqli_query($db, "SELECT * FROM zbblock
			WHERE type = 4
                       ");
$ind    = 0;
$bots   = array();
if ($row = mysqli_fetch_array($result)) {
    do {
        $bots[$ind] = $row["why"];
        $ind++;
    } while ($row = mysqli_fetch_array($result));
}
$botsCount = count($bots);
// step through log file

foreach ($csvFile as $line) {
    $data = str_getcsv($line);
    if ($count === 0) {
        $count = 1;
        continue;
    }
    $record = $data[0];
    if ($record <= $lastrecord) {
        continue;
    }
    $date = $data[1];
    // new day ?
    if ($last != $date) {
        $yesterday = $last;
        if ($firstday === 0) {
            $firstday = 1;
            $last     = $date;
        } else {
            $i = 0;
            print('</table>');
            while ($i < $botsCount) {
                // store totals for each block we dont list           
                mysqli_query($db, "INSERT into zbblock set type = '2', date = '$last', why = '$bots[$i]', total = '$totals[$i]'");
                print("$last $bots[$i] $totals[$i]<br>");
                $i++;
            }
            // store daily total and print summary
            mysqli_query($db, "INSERT into zbblock set type = '3', date = '$last', total = '$tot'");
            print("Total of Blocks on $last = $tot<br>");
            print("To be checked $last = $checktot<br>");
            print('<table><tr style="text-align: center"><td>Record</td><td>Time</td><td style="width: 100px">Query</td><td>URL</td><td>Why Blocked</td><td style="width: 200px">Referral</td></tr>');
        }
        
        // if we have already processed some of this day, store previous totals
        $result   = mysqli_query($db, "SELECT * FROM zbblock
			WHERE type = '2' and date = '$date'
                       ");
        $numrow   = mysqli_num_rows($result);
        $last     = $date;
        $tot      = 0;
        $totals   = array();
        $firstbot = 0;
        $checktot = 0;
        
        if ($numrow != 0) {
            
            $ind = 0;
            
            if ($row = mysqli_fetch_array($result)) {
                do {
                    $totals[$ind] = $row["total"];
                    $ind++;
                } while ($row = mysqli_fetch_array($result));
            }
            // delete previous totals so we can insert new totals later
            $result = mysqli_query($db, "DELETE FROM zbblock
			WHERE type = '2' and date = '$date'
                       ");
            $result = mysqli_query($db, "SELECT * FROM zbblock
			WHERE type = '3' and date = '$date'
                       ");
            $numrow = mysqli_num_rows($result);
            
            if ($numrow != 0) {
                
                $ind = 0;
                
                if ($row = mysqli_fetch_array($result)) {
                    do {
                        $tot = $row["total"];
                        $ind++;
                    } while ($row = mysqli_fetch_array($result));
                }
                $result = mysqli_query($db, "DELETE FROM zbblock
			WHERE type = '3' and date = '$date'
                       ");
            }
        }
    }
    // store block info
    $time  = $data[2];
    $ip    = $data[3];
    $host  = $data[4];
    $score = $data[5];
    $query = $data[6];
    $ref   = $data[7];
    $ua    = $data[8];
    $url   = $data[9];
    $why   = $data[10];
    $tot++;
    $found     = 0;
    // increment uninterested total - one error type only
    $i         = 0;
    while ($i < $botsCount) {
        if ($found === 0) {
            if (stristr($why, $bots[$i])) {
                $totals[$i] = $totals[$i] + 1;
                $found      = 1;
            }
        }
        $i++;
    }
    // print info if we are interested    
    if ($found === 0) {
        if ($firstbot === 0) {
            $firstbot = 1;
        }
        $checktot++;
        print("<tr> <td>$time</td><td>$record</td> <td style=\"width: 100px\">$query</td><td> $url</td><td>$why</td><td style=\"width: 200px\"> $ref</td></tr>");
    }
}
// new day       
$i = 0;
print('</table>');
if ($tot === 0) {
    exit('No Data !!. ZBblock DB not updated.');
} else {
    // create new totals for the day
    while ($i < $botsCount) {
        mysqli_query($db, "INSERT into zbblock set type = '2', date = '$date', why = '$bots[$i]', total = '$totals[$i]'");
        print("$date $bots[$i] $totals[$i]<br>");
        $i++;
    }
    mysqli_query($db, "INSERT into zbblock set type = '3', date = '$date', total = '$tot'");
    $statsdate = $date;
    if ($tot < 20) {
        $statsdate = $yesterday;
    }
    mysqli_query($db, "UPDATE zbblock set date = '$statsdate', total = '$record' where type = 5");
    // print daily summary                    
    print("Total of Blocks on $date = $tot<br>");
    print("To be checked $date = $checktot<br>");
    print("Last Block Id = $record");
    print('</body></html>');
    // send mail if requested
    if ($email) {
        $message = "Number of Blocks yesterday = $tot\r\nTo be checked = $checktot\r\n";
        $message = wordwrap($message, 70, "\r\n");
        mail($email, 'Zbblock stats', $message);
    }
    // save log file and delete it                 
    // what about move/rename?
    copy('zbblock/vault/killed_log.csv', 'zbblock/vault/copykilled_log.csv');
    unlink('zbblock/vault/killed_log.csv');
}
