<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Blood Bank Management System</title>
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/src/css/policy.css">
</head>

<body>

    <!-- Header -->
    <header class="header-bg">
        <div class="container">
            <h1>Privacy Policy</h1>
            <p>Last updated: <?php echo date("F j, Y"); ?></p>
        </div>
    </header>

    <!-- Policy Content -->
    <section style="flex-grow: 1; padding: 2rem 1rem;">
        <div class="container policy-section">
            <p>
                This Privacy Policy explains how the Blood Bank Management System ("we," "our," or "us") collects, uses,
                and protects your information when you use our application. By registering as a donor or a blood bank
                and using the system, you agree to the terms outlined here.
            </p>

            <p>
                When donors register, we collect personal details such as their full name, email ID, password (securely
                hashed), phone number, date of birth, gender, address, pincode, and state code. We also store the
                father’s and mother’s names, blood group, health conditions if any, and additional notes. For each
                donor, a system-generated unique ID is created, which is used for login and record tracking. Similarly,
                when blood banks register, we collect their bank name, official email ID, password (securely hashed),
                bank owner’s name, address, phone number, pincode, and state ID. In addition to personal and
                institutional details, the system also stores activity records, including donation details such as date,
                blood group, notes, donor ID, and bank ID, as well as request records, which include the requested blood
                group, requester ID, request status, and notes.
            </p>

            <p>
                The information collected is primarily used to register and manage both donors and blood banks, maintain
                blood stock records, process and fulfill blood requests, and facilitate donations. It also ensures
                secure authentication and account access, while supporting system monitoring and improvements to enhance
                performance.
            </p>

            <p>
                Certain information may be shared between donors and blood banks where necessary. For example, when a
                donor donates blood, their donor ID is linked to the records of the receiving blood bank. Data may also
                be disclosed to comply with legal obligations, prevent fraud, or ensure user safety. In limited cases,
                trusted third-party service providers, such as hosting or email providers, may have access to data but
                are strictly bound by confidentiality agreements.
            </p>

            <p>
                We take data security seriously and use practices such as password hashing, role-based access control,
                and secure database management to safeguard information. However, no system can be considered entirely
                secure, so we cannot guarantee absolute protection against risks. Personal data is retained for as long
                as a donor or blood bank account remains active or as required by law. Even if accounts are deactivated,
                certain records may be retained for compliance and reporting purposes.
            </p>

            <p>
                Users of the system have rights regarding their personal data. They may access their information,
                request corrections, request deletion of their account, or restrict and object to how their data is
                processed where applicable. To exercise these rights, users can contact us through the details provided
                in this policy.
            </p>

            <p>
                Our service is intended only for individuals aged 18 years or older. We do not knowingly collect
                information from minors, and if such data is discovered, it will be deleted immediately. From time to
                time, this policy may be updated, and any changes will be published with the "last updated" date
                displayed. Major updates may also be communicated through email or system notifications.
            </p>

        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Blood Bank Management System. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>
