<style>
.main-footer {
    background: #333;
    color: white;
    padding: 5px 0;
    margin-top: 50px;
}

.footer-content {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    padding: 0 20px;
}

.footer-logo {
    display: flex;
    align-items: center;
    gap: 15px;
}

.footer-logo p{
    font-size: 15px;
}

.group-logo {
    width: 130px;
    height: 130px;
    object-fit: contain;
}

.footer-credit {
    font-size: 14px;
    color: #bdc3c7;
    margin: 0;
}

.footer-text {
    text-align: right;
}

.footer-text p {
    margin: 0;

}

/* Mobile Responsive */
@media (max-width: 768px) {
    .footer-content {
        flex-direction: column;
        text-align: center;
    }
    
    .footer-logo {
        flex-direction: column;
    }
    
    .footer-text {
        text-align: center;
    }
}
</style>

</main>

<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-text">
            <p>&copy; <?= date("Y") ?> Casa Villagracia Boarding House. All rights reserved.</p>
        </div>
        <div class="footer-logo">
            <p class="footer-credit">Developed by</p> <img src="assets/images/group.png" alt="Group Logo" class="group-logo">
        </div>
    </div>
</footer>

</body>
</html>