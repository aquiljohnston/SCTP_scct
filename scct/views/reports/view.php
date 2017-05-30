<?php

$this->title = 'Report View';
$this->params['breadcrumbs'][] = $this->title;

?>

<table>
    <thead>
        <?php
            $headerRow = array_shift($data);
            foreach($headerRow as $item) {
                echo "<th>$item</th>";
            }
        ?>
    </thead>
    <tbody>
    <?php
        foreach($data as $row) {
            echo "<tr>";
            foreach($row as $item) {
                echo "<td>$item</td>";
            }
            echo "</tr>";
        }
    ?>
    </tbody>
</table>
