function allPurposeMailer(domainName, emailSubject, emailName, displayName) {
    var uno = "to:";
    var dos = "mail";
    var tres = emailName;
    var cuatro = "@";
    var cinco = domainName;
    var seis = emailSubject;
    var siete = displayName;
    document.write("<a href=\"" + dos+uno+tres+cuatro+cinco + "?subject=" + seis + "\">" + siete + "</a>");
}
