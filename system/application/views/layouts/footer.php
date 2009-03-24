</div> <!-- end copy container -->
</div> <!-- end Content container -->
    <div id="footer-container">
        <div id="footer">
            <span class="footerText">
    	    <script type="text/javascript">allPurposeMailer("gnip.com", "Contact Gnip", "info", "Contact Gnip");</script> | 
    	    <a href="/about">About this app</a> | 
    	    <span class="copyright_text">&copy; Copyright 2009 Gnip</span> | <?if ($this->session->userdata('logged_in')) { echo anchor('/admin/logout', 'Log Out');} else { echo anchor('/admin', 'Admin'); }?>
    	    </span>
        </div>
    </div>
</div>  
</body>
</html>
