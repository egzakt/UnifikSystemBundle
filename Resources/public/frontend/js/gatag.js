//	This javascript tags file downloads and external links in Google Analytics.
//	You need to be using the Google Analytics New Tracking Code (ga.js)
//	for this script to work.
//	To use, place this file on all pages just above the Google Analytics tracking code.
//	All outbound links and links to non-html files should now be automatically tracked.
//
//	This script has been provided by Goodwebpractices.com
//	Thanks to ShoreTel, MerryMan and Colm McBarron
//
//	VKI Studios has made changes
//	Mangold Sengers has made changes

var domains = "";

if (document.getElementsByTagName) {
        // Initialize external link handlers
        var hrefs = document.getElementsByTagName("a");
        for (var l = 0; l < hrefs.length; l++) {
                // try {} catch{} block added by erikvold VKI
            try{
                    //protocol, host, hostname, port, pathname, search, hash
                    if (hrefs[l].protocol == "mailto:") {
                            startListening(hrefs[l],"click",trackMailto);
                    } else if (hrefs[l].hostname == location.host) {
                            var path = hrefs[l].pathname + hrefs[l].search;
                            var isDoc = path.match(/\.(?:doc|csv|xls|ppt|pdf|xls|zip|txt)($|\&|\?)/);
                            if (isDoc) {
                                    startListening(hrefs[l],"click",trackExternalLinks);
                            }
                    } else {
                            startListening(hrefs[l],"click",trackExternalLinks);
                    }
            }
            catch(e){
                    continue;
            }
        }
}

function startListening (obj,evnt,func) {
        if (obj.addEventListener) {
                obj.addEventListener(evnt,func,false);

        } else if (obj.attachEvent) {
                obj.attachEvent("on" + evnt,func);

        }
}

function trackMailto (evnt) {
        var href = (evnt.srcElement) ? evnt.srcElement.href : this.href;
        var mailto = "/mailto/" + href.substring(7);
        if (typeof(pageTracker) == "object") pageTracker._trackPageview(mailto);
}

function trackExternalLinks (evnt) {
        var e = (evnt.srcElement) ? evnt.srcElement : this;
        while (e.tagName != "A") {
                e = e.parentNode;
        }

        var lnk = (e.pathname.charAt(0) == "/") ? e.pathname : "/" + e.pathname;

        if (e.search && e.pathname.indexOf(e.search) == -1) lnk += e.search;
        var local = e.hostname;

        if (domains.search(local)== -1){

           if (e.hostname != location.host)
            {
              lnk = "/outbound/" + e.hostname + lnk;
            }
           else
            {  lnk = "/downloads" + lnk;

            }

           if (typeof(pageTracker) == "object") pageTracker._trackPageview(lnk);
        }

}

function excludeDomains(listDomain) {
       domains = listDomain;

}