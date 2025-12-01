<?php include 'header.php'; ?>
<link rel="stylesheet" href="styles.css">

<div class="page-shell">
    <main class="page-main">
        <section class="card announcements-card" style="position: relative;">

            <!-- 🔙 Back Button -->
            <a href="index.php" 
               style="
                    position:absolute;
                    top:20px;
                    left:20px;
                    padding:8px 14px;
                    background:#f3f4f6;
                    color:#111827;
                    border-radius:10px;
                    font-size:0.85rem;
                    text-decoration:none;
                    font-weight:500;
                    box-shadow:0 2px 5px rgba(0,0,0,0.12);
               ">
                ← Back
            </a>

            <h1 class="ann-title" style="text-align:center; margin-top:55px;">
                Send Feedback
            </h1>

            <?php if (isset($_GET['success'])): ?>
                <div class="ann-empty-title" style="color: green; text-align:center;">
                    Feedback sent successfully!
                </div>
            <?php endif; ?>

            <form method="POST" action="submit_feedback.php" style="margin-top: 20px;">
                <label>Name (optional)</label>
                <input type="text" name="name" class="input">

                <label>Email (optional)</label>
                <input type="email" name="email" class="input">

                <label>Message</label>
                <textarea name="message" class="input textarea" required></textarea>

                <button class="btn-submit" type="submit" style="margin-top: 10px; width:100%;">
                    Submit
                </button>
            </form>
        </section>
    </main>
</div>
