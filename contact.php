<?php
// Include header
include 'includes/header.php';

// Handle form submission
$success = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    // Validate form data
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // In a real application, you would send an email here
        // For this example, we'll just show a success message
        $success = "Your message has been sent. We'll get back to you soon!";
    }
}
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Get in touch with us for any questions or inquiries.</p>
    </div>
</section>

<!-- Contact Section -->
<section class="contact">
    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="contact-container">
            <div class="contact-info">
                <h2>Get In Touch</h2>
                <p>Have questions about our cooking classes or recipes? Want to book a private event? We'd love to hear from you!</p>
                
                <div class="contact-details">
                    <div>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>123 Cooking Street, Foodville, FC 12345</span>
                    </div>
                    <div>
                        <i class="fas fa-phone"></i>
                        <span>(123) 456-7890</span>
                    </div>
                    <div>
                        <i class="fas fa-envelope"></i>
                        <span>info@culinaryworkshop.com</span>
                    </div>
                    <div>
                        <i class="fas fa-clock"></i>
                        <span>Monday - Friday: 9am - 6pm<br>Saturday: 10am - 4pm<br>Sunday: Closed</span>
                    </div>
                </div>
                
                <div class="footer-social" style="margin-top: 2rem;">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
            
            <div class="contact-form">
                <form id="contact-form" method="POST">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section style="margin-top: 4rem;">
    <div class="container">
        <div style="width: 100%; height: 400px; background-color: #eee; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
            <p>Map Placeholder - In a real application, you would embed a Google Map here.</p>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>