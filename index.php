<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UOB Student Nationality Data</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.min.css">
    <style>
        table {
            overflow-x: auto;
        }
        th, td {
            text-align: center;
        }
        .filter-form {
            margin-bottom: 1.5rem;
        }
        h1, h2 {
            text-align: center;
        }
        .error, span {
            color: red;
        }

        @media only screen and (max-width: 525px) {
            th {
                font-size: 0.8em;
            }
            td {
                font-size: 0.6em;
            }
        } 
        @media only screen and (max-width: 440px) {
            h1, h2 {
                font-size: 24px;
                margin-bottom: 1em;
            }
            th {
                font-size: 0.67em;
            }
            td {
                font-size: 0.5.5em;
            }
            td, th {
                padding-left: 5px;
                padding-right: 5px;
            } 
        }
    </style>
</head>
<body>
    <main class="container">
        <h1>University of Bahrain Student Enrollment by Nationality</h1>

        <!---------------------------------------------------------------------------------------------------->
        <!------------------------------------------- Filter ------------------------------------------------->
        <!---------------------------------------------------------------------------------------------------->
        <form method="GET" class="filter-form">
        <label for="program">Program: <span> (Required) </span></label>
            <select name="program" id="program">
                <option value="">programs</option>
                <?php
                    $programs = ["bachelor","master's", "diploma", "doctoral"];
                    foreach ($programs as $program) {
                        $selected = (isset($_GET['program']) && $_GET['program'] === $program) ? 'selected' : '';
                        echo "<option value=\"$program\" $selected>$program</option>";
                    }
                ?>
            </select>

            <label for="year">Year:</label>
            <select name="year" id="year">
                <option value="">All</option>
                <?php
                    $years = ["2018-2019", "2019-2020", "2020-2021", "2021-2022"];
                    foreach ($years as $year) {
                        $selected = (isset($_GET['year']) && $_GET['year'] === $year) ? 'selected' : '';
                        echo "<option value=\"$year\" $selected>$year</option>";
                    }
                ?>
            </select>

            <label for="semester">Semester:</label>
            <select name="semester" id="semester">
                <option value="">All</option>
                <?php
                    $semesters = ["First Semester", "Second Semester", "Summer Semester"];
                    foreach ($semesters as $semester) {
                        $selected = (isset($_GET['semester']) && $_GET['semester'] === $semester) ? 'selected' : '';
                        echo "<option value=\"$semester\" $selected>$semester</option>";
                    }
                ?>
            </select>

            <label for="nationality">Nationality:</label>
            <select name="nationality" id="nationality">
                <option value="">All</option>
                <?php
                    $nationalities = ["Bahraini", "GCC National", "Other"];
                    foreach ($nationalities as $nationality) {
                        $selected = (isset($_GET['nationality']) && $_GET['nationality'] === $nationality) ? 'selected' : '';
                        echo "<option value=\"$nationality\" $selected>$nationality</option>";
                    }
                ?>
            </select>

            <label for="college">College:</label>
            <select name="college" id="college">
                <option value="">All</option>
                <?php
                    $colleges = ["College of IT", "College of Science", "College of Engineering", "College of Arts", "College of Business Administration", "College of Health and Sport Sciences", "Bahrain Teachers College", ];
                    foreach ($colleges as $college) {
                        $selected = (isset($_GET['college']) && $_GET['college'] === $college) ? 'selected' : '';
                        echo "<option value=\"$college\" $selected>$college</option>";
                    }
                ?>
            </select>

            <button type="submit">Filter</button>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="secondary">Reset Filters</a>
        </form>

        <?php
            if(!isset($_GET['program'])){echo "<p class='error'>please select program then press filter </p>";} //if user didn't choose Program
            elseif($_GET['program']==""){echo "<p class='error'>please select program then press filter </p>";} //if user choose Program Program
            else { //User choose valid program
                $url = "https://data.gov.bh/api/explore/v2.1/catalog/datasets/01-statistics-of-students-nationalities_updated/records?where=the_programs%20like%20%22{$_GET['program']}%22&limit=100"; //get url based on user chosen program
                $response = file_get_contents($url);
                $data = json_decode($response, true);
                
                if (isset($data['results']) && count($data['results']) > 0) { //if there is retrieved results from API
                    //apply filter(if any exists)
                    $filteredData = array_filter($data['results'], function ($record) {
                        $yearFilter = empty($_GET['year']) || $_GET['year'] === $record['year'];
                        $semesterFilter = empty($_GET['semester']) || $_GET['semester'] === $record['semester'];
                        $nationalityFilter = empty($_GET['nationality']) || $_GET['nationality'] === $record['nationality'];
                        $collegeFilter = empty($_GET['college']) || $_GET['college'] === $record['colleges'];
                        return $yearFilter && $semesterFilter && $nationalityFilter && $collegeFilter;
                    });

                    echo"<h2>Students in {$_GET['program']} Program</h2>";
                    // display Table
                    if (count($filteredData) > 0) {
                        echo '<table role="grid">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>Year</th>';
                        echo '<th>Semester</th>';
                        echo '<th>Nationality</th>';
                        echo '<th>College</th>';
                        echo '<th>Number of Students</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
    
                        foreach ($filteredData as $record) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($record['year']) . '</td>';
                            echo '<td>' . htmlspecialchars($record['semester']) . '</td>';
                            echo '<td>' . htmlspecialchars($record['nationality']) . '</td>';
                            echo '<td>' . htmlspecialchars($record['colleges']) . '</td>';
                            echo '<td>' . htmlspecialchars($record['number_of_students']) . '</td>';
                            echo '</tr>';
                        }
    
                        echo '</tbody>';
                        echo '</table>';
                    } 
                    else { //no data matches filter
                        echo '<p class="error">No data matches your filters. Please adjust the filters and try again.</p>';
                    }
                } 
                else { // issue retrieving data from API
                    echo '<p>No data available at this time. Please try again later.</p>';
                } 
            }
        ?>
    </main>
</body>
</html>