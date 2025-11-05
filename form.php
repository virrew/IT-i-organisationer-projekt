<!DOCTYPE html>
<html>
<head>
    <title>Patient Registration Form</title>
    <style>
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <h2>Patient Registration Form</h2>
    <form action="process_form.php" method="POST">
        <div class="form-group">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" required>
        </div>

        <div class="form-group">
            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" required>
        </div>

        <div class="form-group">
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
        </div>

        <div class="form-group">
            <label for="address">Address:</label>
            <textarea id="address" name="address" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="medicalHistory">Medical History:</label>
            <textarea id="medicalHistory" name="medicalHistory" rows="4"></textarea>
        </div>

        <button type="submit">Submit</button>
    </form>
</body>
</html>