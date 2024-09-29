<?php
/*
Template Name: Admin Template
*/

if (!(is_user_logged_in() && current_user_can('administrator'))) {
    wp_redirect(home_url());
}

$courseList = getSortedCourseList();



get_header();
if (!isset($_GET['course_id'])){

?>
<div class="container">
    <div class="presenceFilters">
        <ul>
            <li><input type="checkbox" value="completed" id="completed" checked><label for="completed">Completed</li>
            <li><input type="checkbox" value="cancelled" id="cancelled"><label for="cancelled">Cancelled</label></li>
            <li><input type="checkbox" value="pending-deposit" id="pending-deposit" checked><label for="pending-deposit">Pending-deposit</label></li>
            <li><input type="checkbox" value="partial-payment" id="partial-payment" checked><label for="partial-payment">Partial-payment</label></li>

        </ul>
    </div>
    <div class="presenceContainer">

    <?php
 
        foreach ($courseList as $courseId => $course){
            ?>
            <div class="prenceTable">
                <h2><a target="_blank" href="https://www.ecole-kinesio.be/wp-admin/post.php?post=<?= $courseId ?>&action=edit" > <?= $course["name"] ?></a></h2>
                <table>
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Numeros de Commande</th>
                            <th>Nom</th>
                            <th>Etat de la comande</th>
                            <th>Prix TVAC </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($course["orders"] as $i => $orders){
                        $tot = 0;
                        $status = "";

                        foreach($orders as $k => $order){
                            $tot += $order['total'];
                            if( $k !== 0){
                                $status .= " + ";
                            }
                            $status .= $order['status'];
                            
                        }
                            ?>
                            <tr class="<?= $order["status"] ?>">

                                <td class="num"><?= $i+1 ?></td>
                                <td><a target="_blank" href="https://www.ecole-kinesio.be/wp-admin/post.php?post=<?= $order['order_id'] ?>&action=edit"><?= $order['order_id'] ?></a></td>
                                <td><a href="mailto:<?= $order["email"] ?>"><?= $order["name"] ?></a></td>
                                <td><?= $status ?></td>
                                <td><?= $tot ?>€</td>

                            </tr>
                            <?php
                    }
                    ?>
                    </tbody>
                </table>
                <div class="gotoPdf">
                    <a href="<?= home_url()?>/admin/?course_id=<?= $courseId ?>">Liste des présences </a>
                </div>
            </div>
            <?php
        }

        // getOrderList(15201);
    ?>

    </div>
</div>

<?php
}
else{
    $courseId = $_GET['course_id'];
    $course = isset($courseList[$courseId]) ? $courseList[$courseId] : "";
    $count = 1;
    if (empty($course)){
        echo "<h1>Aucun éleve inscris pour ce cours</h1>";
    }
    ?>
    <div class="pdfButtons">
        <button id="dlToPsf" data-courseName="<?= $course['name'] ?>">Télécharger en pdf</button>
        <button id="addLine">Ajouter une ligne</button>
    </div>
    <div class="pdfWrapper">
        <div id="pdfContent">
            <h1><?= $course['name'] ?></h1>
            <table>
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Nom</th>
                        <th>Signature</th>
                    </tr>
                </thead>
                <?php foreach($course['orders'] as $order){
                    if ($order['status'] !== "cancelled"){
                        ?>
                        <tr>
                            <td class="num"><?= $count ?></td>
                            <td class="name"><?= $order[0]['name'] ?></td>
                            <td> </td>
                        </tr>
                        <?php
                        $count++;
                    }
                }
                ?>
            </table>
        </div>

    </div>
    <?php
}

get_footer();