<form action="form.php" method="POST">
    <label>Surname:</label><br>
    <input type="text" name="surname"
    value="<?= htmlspecialchars($_POST['surname'] ?? '', ENT_QUOTES, 'UTF-8') ?>"><br><br>

    <label>Email Address:</label><br>
    <input type="email" name="email"
    value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"><br><br>

    <label>Password:</label><br>
    <input type="password" name="password"><br><br>

    <label>Street Address:</label><br>
    <textarea name="address"><?= htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea><br><br>

    <label>State:</label><br>
    <select name="state">
        <option value="">Pilih State</option>
        <option value="New South Wales" <?= ($_POST['state'] ?? '') == 'New South Wales' ? 'selected' : '' ?>>New South Wales</option>
        <option value="Victoria" <?= ($_POST['state'] ?? '') == 'Victoria' ? 'selected' : '' ?>>Victoria</option>
        <option value="Queensland" <?= ($_POST['state'] ?? '') == 'Queensland' ? 'selected' : '' ?>>Queensland</option>
        <option value="Western Australia" <?= ($_POST['state'] ?? '') == 'Western Australia' ? 'selected' : '' ?>>Western Australia</option>
    </select><br><br>

    <input type="hidden" name="country" value="Australia">

    <label>Gender:</label><br>
    <input type="radio" name="gender" value="Male" <?= ($_POST['gender'] ?? '') == 'Male' ? 'checked' : '' ?>> Male
    <input type="radio" name="gender" value="Female" <?= ($_POST['gender'] ?? '') == 'Female' ? 'checked' : '' ?>> Female<br><br>

    <label>Vegetarian?</label>
    <input type="checkbox" name="vegetarian" value="Yes" <?= isset($_POST['vegetarian']) ? 'checked' : '' ?>><br><br>

    <input type="submit" value="Submit">
    <input type="reset" value="Reset">
</form>