<?php

const TIME = array(
  1 => 'AM',
  2 => 'PM'
);

$insert_values = array(
  'name' => NULL,
  'location' => NULL,
  'time' => NULL,
  'ampm' => NULL,
  'when' => NULL
);

// 1. Initialize default page state.
$show_confirmation = False;
$show_form= True;
$show_table = True;

//feedback message CSS classes
$form_feedback_classes = array(
  'name' => 'hidden',
  'location' => 'hidden',
  'time' => 'hidden',
  'ampm' => 'hidden',
  'when' => 'hidden'
);


// values
$form_values = array(
  'name' => '',
  'location' => '',
  'time' => '',
  'ampm' => '',
  'when' => ''
);

// sticky values
$sticky_values = array(
  'name' => '',
  'location' => '',
  'time' => '',
  'am' => '',
  'pm' => '',
  'when' => ''
);

// $nav_home_class = 'active_page';

// open database
$db = open_sqlite_db('secure/site.sqlite');

// query grades table
$result = exec_sql_query($db, 'SELECT * FROM events;');

// get records from query
$records = $result->fetchAll();

// 2. If form was submitted:

  if (isset($_POST['request-event'])) {
    // 3. Store form data as variables.
    $form_values['name'] = trim($_POST['name']); // untrusted
    $form_values['location'] = trim($_POST['location']); // untrusted
    $form_values['time'] = trim($_POST['time']); // untrusted
    $form_values['ampm'] = trim($_POST['ampm']); // untrusted
    $form_values['when'] = trim($_POST['when']); // untrusted

    $insert_values['name'] = ($_POST['name'] == '' ? NULL : trim($_POST['name'])); // untrusted
    $insert_values['location'] = ($_POST['location'] == '' ? NULL : trim($_POST['location'])); // untrusted
    $insert_values['time'] = ($_POST['time'] == '' ? NULL : trim($_POST['time'])); // untrusted
    $insert_values['ampm'] = ($_POST['ampm'] == '' ? NULL : trim($_POST['ampm'])); // untrusted
    $insert_values['when'] = ($_POST['when'] == '' ? NULL : trim($_POST['when'])); // untrusted

    $form_valid = True;

    if ($form_values['name'] == '') {
      $form_valid = False;
      $form_feedback_classes['name'] = '';
    }

    if ($form_values['location'] == '') {
      $form_valid = False;
      $form_feedback_classes['location'] = '';
    }

    if ($form_values['time'] == '') {
      $form_valid = False;
      $form_feedback_classes['time'] = '';
    }

     if ($form_values['ampm'] == '') {
      $form_valid = False;
      $form_feedback_classes['ampm'] = '';
    }

    if ($form_values['when'] == '') {
      $form_valid = False;
      $form_feedback_classes['when'] = '';
    }

    if ($form_valid) {
      $show_confirmation = True;
      $show_form = False;
      $show_table=False;
      $result = exec_sql_query(
        $db,
        "INSERT INTO events (name, location, time, ampm, date)
        VALUES (:names, :locations, :times, :ampms, :whens);",

        array(
          ':locations' => $insert_values['location'], // tainted
          ':names' => $insert_values['name'], // tainted
          ':times' => $insert_values['time'], // tainted
          ':ampms' => $insert_values['ampm'], // tainted
          ':whens' => $insert_values['when'] // tainted
        )
      );


    } else { // 11. Otherwise:
      // 12. Set sticky values and echo them.
      $sticky_values['name'] = $form_values['name'];
      $sticky_values['location'] = $form_values['location'];
      $sticky_values['time'] = $form_values['time'];
      $sticky_values['when'] = $form_values['when'];
      $sticky_values['am'] = $form_values['ampm']==1 ? 'checked' : '';
      $sticky_values['pm'] = $form_values['ampm']==2 ? 'checked' : '';
    }


}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="stylesheet" type="text/css" href="/public/styles/site.css" media="all">
</head>

<body>
  <title class="top">WELCOME TO EMMAUS ROAD CHURCH!</title>
  <div class="title">ER EVENTS</div>

<main class="home">

    <table>

      <?php
      if ($show_table)
      // write a table row for each record
      foreach ($records as $record) { ?>
      <ul>
      <li class="tile">

      <div class="tile-header">
          <div class="tile-name"><?php echo htmlspecialchars( $record["name"] ) ?></div>
          <div class="tile-rest">
              <p class="location"> <?php echo htmlspecialchars( $record["location"] ) ?></p>
            <div class="time">
              <p class="time"> <?php echo htmlspecialchars( $record["time"] ) ?></p>
              <p class="ampm"><?php echo htmlspecialchars(TIME[$record['ampm']]) ?></p>
            </div>
            <p class="date"> <?php echo htmlspecialchars( $record["date"] ) ?></p>
          </div>
      </div>

      </li>
      </ul>
      <?php } ?>
    </table>

    <?php if ($show_form) { ?>
    <div class="form">
      <h3 class="aboutme">Add an event by filling out the form below!</h3>
        <form id="request-form" action="/" method="post" novalidate>

        <p class="feedback <?php echo $form_feedback_classes['name']; ?>">Please provide the name of the event.</p>
          <div class="label-input">
            <label for="name_field">Event Name:</label>
            <input id="name_field" type="text" name="name" value="<?php echo $sticky_values['name']; ?>">
          </div>

          <p class="feedback <?php echo $form_feedback_classes['location']; ?>">Please provide the location of the event.</p>
          <div class="label-input">
            <label for="location_field">Event Location:</label>
            <input id="location_field" type="text" name="location" value="<?php echo $sticky_values['location']; ?>">
          </div>

          <p class="feedback <?php echo $form_feedback_classes['time']; ?>">Please provide the time of the event.</p>
          <div class="label-input">
            <label for="time_field">Event Time:</label>
            <input id="time_field" type="text" name="time" value="<?php echo $sticky_values['time']; ?>">
          </div>

          <p class="feedback <?php echo $form_feedback_classes['ampm']; ?>">Please select AM or PM.</p>
            <div>
            <label for="ampm_field">AM or PM:</label>
              <div>

                <input type="radio" id="am_input" name="ampm" value=1 <?php echo $sticky_values['am']; ?>>
                <label for="am_input">AM</label>
              </div>
              <div>
                <input type="radio" id="pm_input" name="ampm" value=2 <?php echo $sticky_values['pm']; ?>>
                <label for="pm_input">PM</label>
              </div>
            </div>


          <p class="feedback <?php echo $form_feedback_classes['when']; ?>">Please provide the date of the event.</p>
          <div class="label-input">
            <label for="when_field">Date of event:</label>
            <input id="when_field" type="text" name="when" value="<?php echo $sticky_values['when']; ?>">
          </div>

          <div class="align-right">
            <input class="button" type="submit" value="Create Event" name="request-event">
          </div>

    </form>
    </div>
    <?php } ?>


    <?php if ($show_confirmation) { ?>
    <section>
      <h2>EVENT CONFIRMATION</h2>

      <p> Thank you for submitting your event! Your event is <?php echo htmlspecialchars(($form_values['name'])) ?> at <?php echo htmlspecialchars(($form_values['location'])) ?> at <?php echo htmlspecialchars(($form_values['time'])) ?> <?php echo htmlspecialchars(TIME[$form_values['ampm']]) ?> <?php echo htmlspecialchars(($form_values['when'])) ?>.</P>


      <p><a href="/">Submit another event.</a></p>
    </section>
    <?php } ?>
  </main>

</body>

</html>
