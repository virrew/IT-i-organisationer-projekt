<?php
session_start();
?>
<!doctype html>
<html lang="sv">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Boka tid</title>
</head>
<body>

<h1>Bokningsformulär</h1>

<form method="post" action="">
    <label for="name">Namn *</label>
    <input id="name" name="name" type="text" required value="<?php echo e($values['name']); ?>">

    <label for="email">E-post *</label>
    <input id="email" name="email" type="email" required value="<?php echo e($values['email']); ?>">

    <label for="phone">Telefon</label>
    <input id="phone" name="phone" type="tel" value="<?php echo e($values['phone']); ?>">

    <div class="row">
        <div>
            <label for="date">Datum *</label>
            <input id="date" name="date" type="date" required min="<?php echo e($minDate); ?>" value="<?php echo e($values['date']); ?>">
        </div>
        <div>
            <label for="time">Tid *</label>
            <input id="time" name="time" type="time" required step="900" value="<?php echo e($values['time']); ?>">
        </div>
    </div>

    <label for="people">Antal personer *</label>
    <select id="people" name="people" required>
        <?php for ($i = 1; $i <= 20; $i++): ?>
            <option value="<?php echo $i; ?>"<?php if ((int)$values['people'] === $i) echo ' selected'; ?>><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>

    <label for="message">Meddelande / Önskemål</label>
    <textarea id="message" name="message" rows="4"><?php echo e($values['message']); ?></textarea>

    <button type="submit">Skicka bokning</button>
</form>

</body>
</html>