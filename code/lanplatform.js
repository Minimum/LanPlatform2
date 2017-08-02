$(document).ready(lpInitialize);

var lpMessageNum = 0;
var lpHeartbeat = null;

function lpInitialize()
{
    // Data
    $.getJSON("code/live/init.php", {}, lpInitializeData);

    // Nav
    navInitialize();

    // Games
    gamesInitialize();

    // UI
    uiInitialize();

    return;
}

function lpInitializeData(data)
{
    // Set last message id
    lpMessageNum = data.msg;
    
    // News
    newsInitialize(data.news);
    
    // Accounts
    accountInitialize(data.acct);
    
    // Games
    gamesLoad(data.game);
    
    // Guest Accounts
    guestsInitialize(data.lana);
    
    // Start Heartbeat
    lpHeartbeat = setInterval(lpTick, 1000);
    
    return;
}

function lpTick(data)
{
    $.getJSON("code/live/update.php", { msg : lpMessageNum }, lpThink);

    return;
}

function lpThink(data)
{
    // Update last message id
    lpMessageNum = data.last;

    // Parse each message
    $.each(data.msgs, lpMessage);

    return;
}

/*
    Accounts
*/
var accountList = [];
var accountLocal = null;

function accountInitialize(data)
{
    if (data.local != null)
    {
        data.local.id = parseInt(data.local.id);
    }

    accountList = data.accounts;

    accountSetLocal(data.local);

    return;
}

function accountLoginTicket(password)
{
    $.post("code/live/login.php", { type: "ticket", ticket: password }, accountLoginResult, "json");

    return;
}

function accountLoginResult(data)
{
    if(data.success == "1")
    {
        data.acct.id = parseInt(data.acct.id);

        accountSetLocal(data.acct);
    }
    else
    {
        $("#loginInput").effect("highlight", { color: "#dd3e3b" }, 1000);
    }

    return;
}

function accountSetLocal(acct)
{
    accountLocal = acct;

    if (acct != null)
    {
        $(".navAccount").html("Logged in as " + acct.displayname);
    }
    else
    {
        $(".navAccount").html("Login Ticket: <input class=\"inputField\" id=\"loginInput\" type=\"password\" name=\"login\" /> <input id=\"loginSubmit\" type=\"submit\" value=\"Login\" />");

        $("#loginSubmit").click(function () {
            accountLoginTicket($("#loginInput").val());
        });
    }

    adminAccountChange();
    dogeAccountChange();

    return;
}

function accountGetById(id)
{
    var account = null;

    if(accountList != null)
    {
        if(accountList[id] != null && accountList[id].id == id)
        {
            account = accountList[id];
        }
        else
        {
            var len = accountList.length;

            for(var x = 0; x < len; x++)
            {
                if(accountList[x] != null && accountList[x].id == id)
                {
                    account = accountList[x];

                    break;
                }
            }
        }
    }

    return account;
}

/*
    News
*/

function newsInitialize(data)
{
    newsShowActivity(data.homeNews);

    newsAddAdminLinks();

    return;
}

function newsShowActivity(act)
{
    $(".newsBox").removeClass("active");

    $("#newsBox" + act).addClass("active");

    return;
}

function newsMsgChange(message)
{
    newsShowActivity(message);

    return;
}

function newsSetActivity(id)
{
    $.post("code/live/setnews.php", { news: id }, newsSetActivityResult, "json");

    return;
}

function newsSetActivityResult(data)
{
    
}

function newsAddAdminLinks()
{
    $(".setStatusButton").each(function () {
        var link = $(this);

        newsAddAdminLink(link);
    });
}

function newsAddAdminLink(link)
{
    // Click hook
    link.click(function () {
        newsSetActivity($(this).attr("statusid"));
    });
}

/*
    Games
*/

var gamesList = null;

function gamesInitialize()
{
    // Register links
    gamesAddLinks(".gameLink");

    // Register nav buttons
    gamesAddLinks(".gameNavButton");

    return;
}

function gamesLoad(data)
{
    gamesList = data.games;

    return;
}

function gamesAddLinks(classname)
{
    $(classname).each(function () {
        var link = $(this);

        if (link.attr("init") != "1")
        {
            gamesAddLink(link);
        }
    });

    return;
}

function gamesAddLink(link)
{
    // Click hook
    link.click(function () {
        gamesLinkClick($(this));
    });

    // Hover hook
    link.hover(function () {
        gamesLinkHoverOn($(this));
    },
    function () {
        gamesLinkHoverOff($(this));
    });

    link.attr("init", "1");

    return;
}

function gamesLinkHoverOn(link)
{
    link.addClass("hover");

    return;
}

function gamesLinkHoverOff(link)
{
    link.removeClass("hover");

    return;
}

function gamesLinkClick(link)
{
    var gameId = link.attr("game");
    var sectionId = link.attr("section");

    gamesShowSection(gameId, sectionId);

    return;
}

function gamesShowSection(gameId, sectionId)
{
    // Change nav to games section
    $("#mainNavGames").each(function () {
        navBoxClick($(this));
    });

    // Hide all sections
    $(".gameContent").removeClass("active");

    $("#gameContent" + gameId + "-" + sectionId).each(function () {
        // Show the section
        $(this).addClass("active");

        // Set the title of the section
        $("#gamesContentTitle").html($(this).attr("name"));
    });

    // Set all nav buttons as inactive
    $(".gameNavButton").removeClass("active");

    // Set current nav button as active
    $("#gameNavButton" + gameId + "-" + sectionId).addClass("active");

    return;
}

function gamesGetById(id)
{
    var game = null;

    if(gamesList != null)
    {
        if(gamesList[id] != null && gamesList[id].id == id)
        {
            account = gamesList[id];
        }
        else
        {
            var len = gamesList.length;

            for(var x = 0; x < len; x++)
            {
                if(gamesList[x] != null && gamesList[x].id == id)
                {
                    game = gamesList[x];

                    break;
                }
            }
        }
    }

    return game;
}

/*
    Guests
*/
var guestAccounts = [];
var guestCompiledContent = "";

//enum GuestAccountStatus { Available, Unavailable, Taken };

function guestsInitialize(data)
{
    $.each(data.accounts, function (id, account) {
        guestsAddAccount(account);
    });

    $("#homeAcctArea").html(guestCompiledContent);

    $.each(data.accounts, function (id, account) {
        uiAddDropboxes("#homeAcct" + account.id);
    });

    // Checkin/out buttons
    $(".guestCheckButton").hover(function () {
        var button = $(this);

        if(!button.hasClass("inactive"))
        {
            button.addClass("hover");
        }
    },
    function () {
        $(this).removeClass("hover");
    });

    $(".guestCheckButton").click(function () {
        guestsCheckClick($(this));
    });

    return;
}

function guestsAddAccount(account)
{
    guestAccounts[parseInt(account.id)] = account;

    guestCompiledContent += guestsCreateAccountHtml(account);

    return;
}

function guestsCreateAccountHtml(account)
{
    var userId = parseInt(account.checkout);

    var output = "<span class=\"dynamicButton\" id=\"homeAcct" + account.id + "\"><span class=\"dynamicButtonSymbol\" id=\"homeAcct" + account.id + "Symbol\">+ </span>" + account.name + " (";
    
    // Status
    if (userId == 0)
    {
        output += "<span style=\"color:#88d184\" id=\"homeAcct" + account.id + "Status\">Available</span>";
    }
    else if(accountLocal != null && userId == accountLocal.id)
    {
        output += "<span style=\"color:#88d184\" id=\"homeAcct" + account.id + "Status\">Claimed</span>";
    }
    else
    {
        output += "<span style=\"color:#dd3e3b\" id=\"homeAcct" + account.id + "Status\">Checked out by " + account.checkoutName + "</span>";
    }

    output += ")</span><br /><div class=\"dynamicBox\" id=\"homeAcct" + account.id + "Box\">";

    // Checkin/out Button & Password
    if (userId == 0)
    {
        output += "<div style=\"margin-left: 18px\"><div class=\"guestCheckButton\" id=\"homeAcct" + account.id + "Check\" target=\"" + account.id + "\">[Checkout]</div>Password: <span id=\"homeAcct" + account.id + "Pass\" style=\"font-weight: bold\">PROTECTED</span><br />Games:<div style=\"margin-left: 18px\">";
    }
    else if (accountLocal != null && userId == accountLocal.id)
    {
        output += "<div style=\"margin-left: 18px\"><div class=\"guestCheckButton\" id=\"homeAcct" + account.id + "Check\" target=\"" + account.id + "\">[Checkin]</div>Password: <span id=\"homeAcct" + account.id + "Pass\" style=\"font-weight: bold\">" + account.password + "</span><br />Games:<div style=\"margin-left: 18px\">";
    }
    else
    {
        output += "<div style=\"margin-left: 18px\"><div class=\"guestCheckButton inactive\" id=\"homeAcct" + account.id + "Check\" target=\"" + account.id + "\">[Checkout]</div>Password: <span id=\"homeAcct" + account.id + "Pass\" style=\"font-weight: bold\">PROTECTED</span><br />Games:<div style=\"margin-left: 18px\">";
    }
    
    var numGames = account.games.length;

    for(var x = 0; x < numGames; x++)
    {
        var game = gamesGetById(account.games[x]);

        if(game != null)
        {
            if(x != 0)
            {
                output += "<br />";
            }

            output += game.name;
        }
    }

    output += "<br /></div></div></div>";

    return output;
}

function guestsSetStatus(account, color, text)
{
    // Set color
    $("#homeAcct" + account.id + "Status").css("color", color);

    // Set text
    $("#homeAcct" + account.id + "Status").text(text);

    return;
}

function guestsCheckout(account)
{
    $.post("code/live/checkoutaccount.php", { acct: account.id }, guestsCheckoutResult, "json");

    return;
}

function guestsCheckoutResult(data)
{
    return;
}

function guestsCheckin(account)
{
    $.post("code/live/checkinaccount.php", { acct: account.id }, guestsCheckinResult, "json");

    return;
}

function guestsCheckinResult(data)
{
    return;
}

function guestsCheckClick(button)
{
    var account = guestAccounts[parseInt(button.attr("target"))];

    if(account != null)
    {
        if(account.checkout == 0)
        {
            guestsCheckout(account);
        }
        else if(account.checkout == accountLocal.id)
        {
            guestsCheckin(account);
        }
    }

    return;
}

function guestsMsgCheckout(message)
{
    var data = message.split("|");
    var accountId = parseInt(data[0]);
    var account = guestAccounts[accountId];
    var userId = parseInt(data[1]);
    var userName = data[2];

    // Change javascript data
    if (guestAccounts[accountId] != null)
    {
        guestAccounts[accountId].checkout = userId;
        guestAccounts[accountId].checkoutName = userName;
    }

    // Change html
    if (accountLocal != null && accountLocal.id == userId)
    {
        $("#homeAcct" + accountId + "Status").text("Claimed");

        $("#homeAcct" + accountId + "Check").text("[Checkin]");

        if (account != null)
        {
            $("#homeAcct" + accountId + "Pass").text(account.password);
        }
    }
    else
    {
        $("#homeAcct" + accountId + "Status").text("Checked out by " + userName);
        $("#homeAcct" + accountId + "Status").css("color", "#dd3e3b");

        $("#homeAcct" + accountId + "Check").text("[Checkout]");
        $("#homeAcct" + accountId + "Check").removeClass("hover");
        $("#homeAcct" + accountId + "Check").addClass("inactive");
    }

    return;
}

function guestsMsgCheckin(message)
{
    var accountId = parseInt(message);

    // Change javascript data
    if (guestAccounts[accountId] != null)
    {
        guestAccounts[accountId].checkout = 0;
        guestAccounts[accountId].checkoutName = "";
    }

    // Change html
    $("#homeAcct" + accountId + "Status").text("Available");
    $("#homeAcct" + accountId + "Status").css("color", "#88d184");

    $("#homeAcct" + accountId + "Check").text("[Checkout]");
    $("#homeAcct" + accountId + "Check").removeClass("inactive");

    $("#homeAcct" + accountId + "Pass").text("PROTECTED");

    return;
}

/*
    Music
*/

var musicSongs = [];
var musicPlaylists = [];

// 1: global playlist
// 2: personal queue

function musicInitialize(data)
{
    
}

/*
    Admin
*/
function adminAccountChange()
{
    if(accountLocal != null)
    {
        if (accountLocal.type == "1")
        {
            adminShowSections();
        }
        else
        {
            adminHideSections();
        }
    }
    else
    {
        adminHideSections();
    }

    return;
}

function adminShowSections()
{
    $("#mainNavAdmin").removeClass("hidden");

    $("#newsQuickSetSection").removeClass("hidden");

    return;
}

function adminHideSections()
{
    $("#mainNavAdmin").addClass("hidden");

    $("#newsQuickSetSection").addClass("hidden");

    return;
}

/*
    Doge
*/
function dogeAccountChange()
{

}

function dogeShowBackground(show)
{
    if (show == null)
    {
        dogeShowBackground(!dogeBackgroundActive);
    }
    else if(show)
    {
        $("html").addClass("doge");

        dogeBackgroundActive = true;
    }
    else
    {
        $("html").removeClass("doge");

        dogeBackgroundActive = false;
    }

    return;
}

$(window).konami({
    code: [38, 38, 40, 40, 37, 39, 37, 39], // up up down down left right left right
    cheat: function () {
        $("#mainNavDoge").removeClass("hidden");
        dogeShowBackground();
    }
});

var dogeBackgroundActive = false;

/*
    Navigation
*/

function navInitialize()
{
    // Hover
    $(".navBox").hover(function () {
        navBoxHoverOn($(this));
    },
    function () {
        navBoxHoverOff($(this));
    });

    // Click
    $(".navBox").click(function () {
        navBoxClick($(this));
    });

    return;
}

function navBoxHoverOn(button)
{
    var buttonId = button.attr("id");

    $("#" + buttonId + "Button").addClass("hover");

    return;
}

function navBoxHoverOff(button)
{
    var buttonId = button.attr("id");

    $("#" + buttonId + "Button").removeClass("hover");

    return;
}

function navBoxClick(button)
{
    // Set all nav buttons' statuses to OFF
    $(".navStatus").removeClass("active");

    // Set this button's alert to OFF
    var buttonId = button.attr("id");

    $("#" + buttonId + "Status").removeClass("alert");

    // Set this button's status to ON
    $("#" + buttonId + "Status").addClass("active");

    // Hide all sections
    $(".navContent").removeClass("active");

    // Display this section
    $("#" + buttonId + "Content").addClass("active");

    return;
}

function navBoxAlert(section)
{
    if (!$("#" + section + "Status").hasClass("active"))
    {
        $("#" + section + "Status").addClass("alert");
    }

    return;
}

/*
    Misc UI
*/

function uiInitialize()
{
    uiAddDropboxes(".dynamicButton");

    return;
}

function uiAddDropboxes(classname)
{
    $(classname).each(function () {
        var box = $(this);

        if (box.attr("init") != "1")
        {
            uiAddDropbox(box);
        }
    });

    return;
}

function uiAddDropbox(button) {
    button.click(function () {
        uiDropboxClick($(this));
    });

    button.hover(function () {
        uiDropboxHoverOn($(this));
    },
    function () {
        uiDropboxHoverOff($(this));
    });

    return;
}

function uiAddDropboxId(buttonId)
{
    $("#" + buttonId).click(function () {
        uiDropboxClick($(this));
    });

    return;
}

function uiDropboxClick(button)
{
    if (button.attr("toggle") == "1")
    {
        // Set toggle variable to off
        button.attr("toggle", "0");
        
        // Change symbol to off position
        $("#" + button.attr("id") + "Symbol").html("+ ");

        // Hide box content
        $("#" + button.attr("id") + "Box").removeClass("active");
    }
    else
    {
        // Set toggle variable to on
        button.attr("toggle", "1");

        // Change symbol to on position
        $("#" + button.attr("id") + "Symbol").html("- ");

        // Show box content
        $("#" + button.attr("id") + "Box").addClass("active");
    }

    return;
}

function uiDropboxHoverOn(button)
{
    button.addClass("hover");

    return;
}

function uiDropboxHoverOff(button)
{
    button.removeClass("hover");

    return;
}

/*
    Messages
*/
function lpMessage(id, message)
{
    switch (message.name)
    {
        case "lanAcctCheckout":
            {
                guestsMsgCheckout(message.data);

                break;
            }

        case "lanAcctCheckin":
            {
                guestsMsgCheckin(message.data);

                break;
            }

        case "newsChange":
            {
                newsMsgChange(message.data);

                break;
            }

        case "dogeBackground":
            {
                if (message.data.show == "0")
                {
                    dogeShowBackground(false);
                }
                else
                {
                    dogeShowBackground(true);
                }

                break;
            }
    }

    return;
}

$(document).ready(function () {
    $('.dropMenu').dropit();
});