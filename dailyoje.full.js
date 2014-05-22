var OJE = new function()
{
    this.Twitter = new function()
    {
        this.startAuthenticate = function()
        {
            OJE.Authenticate.indicateLoggingIn(true);
            $.ajax({
                url: OJE.rootUrl + "WebRouter.php",
                data: OJE.Twitter.StartOAuthRequest(),
                type: "POST",
                dataType: "json"
            })
                .done(function(response)
                {
                    if (response && response.data && response.data.authorizeUrl)
                    {
                        window.location.href = response.data.authorizeUrl;
                    }
                    else
                    {
                        OJE.showMessage("Unfortunately an error occurred with Twitter. Please try again.");
                    }
                })
                .fail(function()
                {
                    OJE.Authenticate.indicateLoggingIn(false);
                    OJE.showMessage("Unfortunately an error occurred with Twitter. Please try again.");
                });
        };
        this.StartOAuthRequest = function()
        {
            return {requestType: "Twitter_StartOAuth", currentUrl: document.URL};
        };
    };
    this.Authenticate = new function()
    {
        this.toggleLoginDialog = function(show)
        {
            if (show)
            {
                $("#loginDialog").show();
            }
            else
            {
                $("#loginDialog").hide();
            }
        };
        this.validateAndProcessLogin = function(event)
        {
            if (event && event.preventDefault)
            {
                event.preventDefault();
            }

            var email = $("#loginEmail").val();
            var password = $("#loginPassword").val();

            var hasError = false;
            if (!email)
            {
                hasError = true;
                OJE.Authenticate.setInputState("loginEmail", true);
            }
            else
            {
                OJE.Authenticate.setInputState("loginEmail", false);
            }

            if (!password)
            {
                hasError = true;
                OJE.Authenticate.setInputState("loginPassword", true);
            }
            else
            {
                OJE.Authenticate.setInputState("loginPassword", false);
            }

            if (!hasError)
            {
                OJE.Authenticate.indicateLoggingIn(true);
                $.ajax({
                    url: OJE.rootUrl + "WebRouter.php",
                    data: OJE.Authenticate.loginRequest(email, password),
                    type: "POST",
                    dataType: "json"
                })
                    .done(function(response)
                    {
                        if (response && response.data)
                        {
                            if (!response.data.validCredentials)
                            {
                                OJE.Authenticate.indicateLoggingIn(false);
                                OJE.Authenticate.setLoginError("Invalid Credentials.");
                                OJE.Authenticate.setInputState("loginEmail", true);
                                OJE.Authenticate.setInputState("loginPassword", true);
                            }
                            else
                            {
                                OJE.Authenticate.setLoginError("");
                                OJE.Authenticate.setInputState("loginEmail", false);
                                OJE.Authenticate.setInputState("loginPassword", false);

                                location.reload();
                            }

                        }
                        else
                        {
                            OJE.Authenticate.setLoginError("Unknown error occured. #340311");
                        }

                    })
                    .fail(function()
                    {
                        OJE.Authenticate.indicateLoggingIn(false);
                        OJE.Authenticate.setLoginError("Unknown Error Occurred. #340312");
                    });
            }
        };
        this.validateAndProcessRegister = function(event)
        {
            if (event && event.preventDefault)
            {
                event.preventDefault();
            }

            var email = $("#registerEmail").val();
            var password = $("#registerPassword").val();

            var hasError = false;
            if (!email)
            {
                hasError = true;
                OJE.Authenticate.setInputState("registerEmail", true);
            }
            else
            {
                OJE.Authenticate.setInputState("registerEmail", false);
            }

            if (!password)
            {
                hasError = true;
                OJE.Authenticate.setInputState("registerPassword", true);
            }
            else if (password.length < 6)
            {
                hasError = true;
                OJE.Authenticate.setInputState("registerPassword", true);
                OJE.Authenticate.setRegisterError("Password must be 6 characters or more.")
            }
            else
            {
                OJE.Authenticate.setInputState("registerPassword", false);
            }

            if (!hasError)
            {
                OJE.Authenticate.indicateLoggingIn(true);
                $.ajax({
                    url: OJE.rootUrl + "WebRouter.php",
                    data: OJE.Authenticate.registerRequest(email, password),
                    type: "POST",
                    dataType: "json"
                })
                    .done(function(response)
                    {
                        if (response && response.data)
                        {
                            if (response.data.invalidEmail)
                            {
                                OJE.Authenticate.indicateLoggingIn(false);
                                OJE.Authenticate.setRegisterError("Email is not a valid email address.");
                                OJE.Authenticate.setInputState("registerEmail", true);
                                OJE.Authenticate.setInputState("registerPassword", false);
                            }
                            else if (response.data.emailExists)
                            {
                                OJE.Authenticate.indicateLoggingIn(false);
                                OJE.Authenticate.setRegisterError("Email is already registered.");
                                OJE.Authenticate.setInputState("registerEmail", true);
                                OJE.Authenticate.setInputState("registerPassword", false);
                            }
                            else if (response.data.passwordTooShort)
                            {
                                OJE.Authenticate.indicateLoggingIn(false);
                                OJE.Authenticate.setRegisterError("Email is already registered.");
                                OJE.Authenticate.setInputState("registerEmail", false);
                                OJE.Authenticate.setInputState("registerPassword", true);
                            }
                            else if (response.data.success)
                            {
                                OJE.Authenticate.setRegisterError("");
                                OJE.Authenticate.setInputState("registerEmail", false);
                                OJE.Authenticate.setInputState("registerPassword", false);
                                location.reload();
                            }
                            else
                            {
                                OJE.Authenticate.indicateLoggingIn(false);
                                OJE.Authenticate.setRegisterError("Unknown error occured. #340210");
                            }
                        }
                        else
                        {
                            OJE.Authenticate.indicateLoggingIn(false);
                            OJE.Authenticate.setRegisterError("Unknown error occured. #340211");
                        }

                    })
                    .fail(function(err1, err2, err3)
                    {
                        OJE.Authenticate.indicateLoggingIn(false);
                        OJE.Authenticate.setRegisterError("Unknown Error Occurred. #340212");
                    });
            }
        };
        this.setInputState = function(elementId, hasError)
        {
            if (hasError)
            {
                $("#" + elementId).addClass("input-error");
                OJE.Authenticate.shakeElement("elementId");
            }
            else
            {
                $("#" + elementId).removeClass("input-error");
            }
        };
        this.toggleRegisterForm = function(show)
        {
            if (show)
            {
                $("#loginFormContainer").hide();
                $("#registerFormContainer").show();
                $("#registerText").hide();
                $("#loginRibbon").text("Register");
            }
            else
            {
                $("#loginFormContainer").show();
                $("#registerFormContainer").hide();
                $("#registerText").show();
                $("#loginRibbon").text("Login");
            }
        };
        this.setRegisterError = function(message)
        {
            $("#registerError").text(message);
        };
        this.setLoginError = function(message)
        {
            $("#loginError").text(message);
        };
        this.shakeElement = function(elementId)
        {
            var l = 10;

            for (var i = 0; i < 6; i++)
            {
                $("#" + elementId).animate({ 'margin-left': "+=" + ( l = -l ) + 'px' }, 60);
            }

        };
        this.indicateLoggingIn = function(yes)
        {
            if (yes)
            {
                $("#loginFormLoadingIndicator").show();
                $("#loginContent").hide();
            }
            else
            {
                $("#loginFormLoadingIndicator").hide();
                $("#loginContent").fadeIn();
            }
        };
        this.registerRequest = function(email, password)
        {
            return {requestType: "Authenticate_Register", email: email, password: password};
        };
        this.loginRequest = function(email, password)
        {
            return {requestType: "Authenticate_Login", email: email, password: password};
        };
    };

    this.rootUrl = "";
    this.element = null;
    this.elementTitle = null;
    this.tagWhiteList = ["p", "div"];
    this.placeholderText = "";
    this.placeholderTitle = "";
    this.errors = [];
    this.messages = [];
    this.loggedIn = false;
    this.edit = false;
    this.menuStuck = false;

    this.Request = new function()
    {
        this.AutoSaveRequest = function()
        {
            return {requestType: "Note_AutoSave", noteText: OJE.element.html(), noteTitle: OJE.elementTitle.text(), noteId: OJE.Note.noteId};
        };
        this.FetchNotesRequest = function(pageNumber, ajaxRequest, searchKey)
        {
            searchKey = searchKey || "";
            return {requestType: "Note_FetchNotes", searchKey: searchKey, pageNumber: pageNumber, resultsPerPage: OJE.Note.notesPerPage, ajaxRequest: ajaxRequest};
        };
        this.LoadNoteRequest = function(noteId)
        {
            return {requestType: "Note_LoadNote", noteId: noteId};
        };
        this.UpdatePrivacyRequest = function()
        {
            return {requestType: "Note_UpdatePrivacy", notePrivacy: $("#privacySettingsSelect").val(), noteId: OJE.Note.noteId};
        };
        this.DeleteNoteRequest = function(noteId)
        {
            return {requestType: "Note_DeleteNote", noteId: noteId};
        };
    };

    this.Note = new function()
    {
        this.noteId = "";

        this.notesPerPage = 5;
        this.currentPage = 1;

        this.pagesDrawn = false;

        this.currentSearchKey = "";

        this.Save = new function()
        {
            this.autoSaveTimeout = null;
            this.autoSaveInterval = 5000;
            this.midSave = false;
            this.lastSaveText = "";
            this.lastSaveTitle = "";

            this.shouldTriggerAutoSave = function()
            {
                var trigger = false;
                var isDirty = OJE.Note.isDataDirty();
                if (OJE.Note.noteId && isDirty)
                {
                    trigger = true;
                }
                else if (OJE.Note.noteId && !isDirty)
                {
                    OJE.Note.markAsUnDirty();
                }
                else if (!OJE.Note.noteId
                    && OJE.element.html() != OJE.placeholderText
                    && OJE.element.text().length > 3)
                {
                    //Only trigger initial save if we have edited both title and text, and text is longer than 30 chars.
                    trigger = true;
                }
                return trigger;
            };
            this.triggerAutoSave = function()
            {
                if (OJE.Note.Save.autoSaveTimeout || !OJE.edit)
                {
                    //We already are waiting on our next autosave request.
                    return;
                }

                if (OJE.Note.Save.shouldTriggerAutoSave())
                {
                    OJE.Note.Save.sendAutoSaveRequest();
                }
                else
                {
                    OJE.Note.Save.autoSaveTimeout = setTimeout(function()
                    {
                        OJE.Note.Save.autoSaveTimeout = null;
                        OJE.Note.Save.triggerAutoSave();
                    }, OJE.Note.Save.autoSaveInterval);
                }
            };
            this.sendAutoSaveRequest = function()
            {
                var deferred = $.Deferred();

                if (OJE.Note.Save.midSave)
                {
                    return deferred.resolve().promise();
                }

                var titleChanged = false;
                if (OJE.elementTitle.text() != OJE.Note.Save.lastSaveTitle)
                {
                    titleChanged = true;
                }

                OJE.Note.Save.lastSaveText = OJE.element.html();
                OJE.Note.Save.lastSaveTitle = OJE.elementTitle.text();

                OJE.Note.Save.midSave = true;
                $.ajax({
                    url: OJE.rootUrl + "WebRouter.php",
                    data: OJE.Request.AutoSaveRequest(),
                    type: "POST",
                    dataType: "json"
                })
                    .done(function(response)
                    {
                        OJE.Note.markAsUnDirty();
                        OJE.Note.toggleShareButton();

                        if (OJE.validateAjaxError(response))
                        {
                            if (response.data && response.data.noteId)
                            {
                                if (!OJE.Note.noteId && response.data && response.data.noteId)
                                {
                                    //First time saving
                                    OJE.Note.noteId = response.data.noteId;
                                    OJE.Note.fetchNotes(OJE.Note.currentPage, true);
                                    deferred.resolve();
                                }
                                else if (OJE.Note.noteId && OJE.Note.noteId == response.data.noteId)
                                {
                                    if (titleChanged)
                                    {
                                        OJE.Note.fetchNotes(OJE.Note.currentPage, true);
                                    }
                                    //We are all good, we saved and received back the same noteId.
                                    deferred.resolve();

                                }
                                else
                                {
//                                OJE.showMessage("Problem autosaving. Please report Error Code #AutoSave_INVALID_ID");

                                    //Don't show error, could just be that we clicked to a different note before this call returned.

                                    deferred.reject();
                                }

                                //Show delete link
                                $("#deleteLink").show();
                                $("#exportLink").show();

                            }
                        }

                    })
                    .fail(function(jqXHR, textStatus, errorThrown)
                    {
                        deferred.reject();
                        OJE.showMessage("Problem autosaving. Did you lose your connection?");
                    })
                    .always(function()
                    {
                        OJE.Note.Save.midSave = false;
                        setTimeout(function()
                        {
                            OJE.Note.Save.triggerAutoSave()
                        }, OJE.Note.Save.autoSaveInterval);
                    });
                return deferred.promise();

            };
        };

        /**
         * DELETE
         */

        this.toggleDeleteWarning = function(show)
        {
            if (show)
            {
                $("#deleteBox").show();
            }
            else
            {
                $("#deleteBox").hide();
            }
        };

        /**
         * LOAD NOTE
         */

        this.indicateNoteDeleting = function(yes)
        {
            if (yes)
            {
                OJE.element.hide();
                OJE.elementTitle.hide();
                OJE.Note.toggleDeleteWarning(false);
                $("#footer").hide();
                $("#loadingNoteIndicator").show();
            }
            else
            {
                OJE.element.show();
                OJE.elementTitle.show();
                $("#loadingNoteIndicator").hide();
                $("#footer").show();
            }
        };

        this.deleteCurrentNote = function()
        {

            if (OJE.Note.Save.autoSaveTimeout)
            {
                clearTimeout(OJE.Note.Save.autoSaveTimeout);
            }

            if (!OJE.Note.noteId)
            {
                OJE.showMessage("There was an error deleting your note. Please report Error #DeleteNote_NONID")
            }
            else
            {
                OJE.Note.indicateNoteDeleting(true);

                $.ajax({
                    url: OJE.rootUrl + "WebRouter.php",
                    data: OJE.Request.DeleteNoteRequest(OJE.Note.noteId),
                    type: "POST",
                    dataType: "json"
                })
                    .done(function(response)
                    {
                        if (OJE.validateAjaxError(response))
                        {
                            //Note succesfully deleted, forward to new note page
                            window.location = OJE.rootUrl + "?message=Note_DeleteSuccess";
                        }
                        else
                        {
                            OJE.Note.indicateNoteDeleting(false);
                        }

                    })
                    .fail(function()
                    {
                        OJE.Note.indicateNoteDeleting(false);
                        OJE.showMessage("Problem deleting your note. Did you lose your connection?");
                    });
            }

        };

        /**
         * EXPORT
         */
        this.toggleExport = function(yes)
        {
            if (yes)
            {
                $("#exportDialog").show();
            }
            else
            {
                $("#exportDialog").hide();
            }
        };
        this.exportHtml = function()
        {
            OJE.Note.exportNote("html");
        };
        this.exportText = function()
        {
            OJE.Note.exportNote("text");
        };
        this.exportNote = function(type)
        {
            location.href = OJE.rootUrl + "export.php?type=" + type + "&noteId=" + OJE.Note.noteId;
        };
        /**
         * SHARE
         */

        this.showShare = function()
        {
            $("#shareUrl").val(OJE.rootUrl + "v/" + OJE.Note.noteId);
            $("#shareUrl").click(function()
            {
                $(this).select();
            });
            $("#shareBox").show();
        };
        this.hideShare = function()
        {
            $("#shareBox").hide();
        };
        this.toggleShareButton = function()
        {
            if (OJE.Note.noteId && $("#privacySettingsSelect").val() == "public")
            {
                $("#shareButton").show();
            }
            else
            {
                $("#shareButton").hide();
            }
        };


        /**
         * LOAD NOTE
         */

        this.indicateNoteLoading = function(yes)
        {
            if (yes)
            {
                OJE.element.hide();
                OJE.elementTitle.hide();
                $("#loadingNoteIndicator").show();
            }
            else
            {
                OJE.element.show();
                OJE.elementTitle.show();
                $("#loadingNoteIndicator").hide();

                //Remove initial title class
                OJE.elementTitleBlur();
                //Focus on text
                OJE.element.focus();

                OJE.refreshEditState();
            }
        };
        this.loadNote = function(noteId)
        {

            OJE.Note.indicateNoteLoading(true);

            if (OJE.Note.isDataDirty() && OJE.Note.noteId)
            {
                OJE.Note.Save.sendAutoSaveRequest()
                    .pipe(function()
                    {
                        return OJE.Note.sendLoadNoteRequest(noteId);
                    })
                    .always(function()
                    {
                        OJE.Note.indicateNoteLoading(false);
                    });
            }
            else
            {
                OJE.Note.sendLoadNoteRequest(noteId)
                    .always(function()
                    {
                        OJE.Note.indicateNoteLoading(false);
                    });
            }

        };

        this.sendLoadNoteRequest = function(noteId)
        {
            var deferred = $.Deferred();

            $.ajax({
                url: OJE.rootUrl + "WebRouter.php",
                data: OJE.Request.LoadNoteRequest(noteId),
                type: "POST",
                dataType: "json"
            })
                .done(function(response)
                {
                    if (OJE.validateAjaxError(response))
                    {

                        if (response.data)
                        {
                            OJE.Note.noteId = response.data.noteId;

                            //Force lastSaveText/Title so we don't autosave when we really don't have any changes.
                            OJE.Note.Save.lastSaveText = response.data.noteText;
                            OJE.Note.Save.lastSaveTitle = response.data.noteTitle;

                            //Set text/title on UI
                            OJE.element.html(response.data.noteText);
                            OJE.elementTitle.text(response.data.noteTitle);

                            //Select privacy
                            $("#privacySettingsSelect").val(response.data.notePrivacy).prop('selected', true);

                            //Update word/char counts
                            OJE.Note.updateCounts();
                            //Show delete link
                            $("#deleteLink").show();
                            $("#exportLink").show();

                            OJE.Note.markAsUnDirty();
                            OJE.Note.toggleShareButton();

                            location.hash = OJE.Note.noteId;

                            OJE.Note.Save.triggerAutoSave();


                        }
                    }

                })
                .fail(function()
                {
                    OJE.showMessage("Problem Loading your note. Did you lose your connection?");
                })
                .always(function()
                {
                    deferred.resolve();
                })

            return deferred.promise();
        };

        /**
         * FETCH NOTES
         */

        this.indicateFetchingNotes = function(yes)
        {
            if (yes)
            {
//                $("#noteResults").animate({opacity: 0},5);
                $("#noteFetchLoading").show();

            }
            else
            {
                $("#noteFetchLoading").hide();
//                $("#noteResults").animate({opacity: 1}, 5);
            }
        };
        this.fetchNotesPagination = function(pageNumber)
        {
            OJE.Note.fetchNotes(pageNumber, OJE.edit, OJE.Note.currentSearchKey);
        };
        this.fetchNotes = function(pageNumber, ajaxRequest, searchKey)
        {
            pageNumber = pageNumber || 1;
            searchKey = searchKey || "";

            OJE.Note.indicateFetchingNotes(true);
            $.ajax({
                url: OJE.rootUrl + "WebRouter.php",
                data: OJE.Request.FetchNotesRequest(pageNumber, ajaxRequest, searchKey),
                type: "POST",
                dataType: "json"
            })
                .done(function(response)
                {
                    if (OJE.validateAjaxError(response))
                    {
                        if (response.data && response.data.html)
                        {
                            OJE.Note.currentPage = pageNumber;
                            $("#noteResults").html(response.data.html);

                            if ((!OJE.Note.pagesDrawn && response.data.totalResults > OJE.Note.notesPerPage) || searchKey)
                            {
                                OJE.Note.pagesDrawn = true;
                                $(function()
                                {

                                    $("#notePagination").pagination({
                                        items: response.data.totalResults,
                                        itemsOnPage: OJE.Note.notesPerPage,
                                        cssStyle: 'compact-theme',
                                        onPageClick: OJE.Note.fetchNotesPagination,
                                        nextText: "",
                                        currentPage: pageNumber,
                                        prevText: "",
                                        displayedPages: 3,
                                        edges: 1,
                                        hrefTextPrefix: ""
                                    });


                                });
                            }
                        }
                    }
                })
                .fail(function()
                {
                    OJE.showMessage("Problem fetching notes. Did you lose your connection?");
                })
                .always(function()
                {
                    OJE.Note.indicateFetchingNotes(false);
                    setTimeout(function()
                    {
                        OJE.Note.Save.triggerAutoSave()
                    }, OJE.Note.Save.autoSaveInterval);
                });
        };


        this.isDataDirty = function()
        {
            var title = OJE.elementTitle.text();
            if (title != OJE.Note.Save.lastSaveTitle)
            {
                return true;
            }

            var text = OJE.element.html();

            if (text != OJE.Note.Save.lastSaveText)
            {
                return true;
            }

            return false;

        };
        this.markAsUnDirty = function()
        {
            $("#saveStatus").html("Saved");
            $("#saveStatus").removeClass("save-status-unsaved");
        };
        this.markAsDirty = function()
        {
            $("#saveStatus").html("Unsaved");
            $("#saveStatus").addClass("save-status-unsaved");
        };
        this.updatePrivacy = function()
        {
            $("#privacyLoader").show();
            $("#privacySuccess").hide();
            if (OJE.Note.noteId)
            {
                OJE.Note.sendUpdatePrivacyRequest();
            }
            else
            {
                OJE.Note.Save.sendAutoSaveRequest()
                    .done(function()
                    {
                        OJE.Note.sendUpdatePrivacyRequest();
                    })
                    .fail(function()
                    {
                        $("#privacyLoader").hide();
                    })
            }


        };
        this.sendUpdatePrivacyRequest = function()
        {
            $.ajax({
                url: OJE.rootUrl + "WebRouter.php",
                data: OJE.Request.UpdatePrivacyRequest(),
                type: "POST",
                dataType: "json"
            })
                .done(function(response)
                {
                    OJE.Note.toggleShareButton();
                    $("#privacyLoader").hide();
                    if (OJE.validateAjaxError(response))
                    {
                        $("#privacySuccess").show();
                        setTimeout(function()
                        {
                            $("#privacySuccess").hide();
                        }, 5000);
                    }
                })
                .fail(function()
                {
                    $("#privacyLoader").hide();
                    OJE.showMessage("Problem updating privacy. Did you lose your connection?");
                });
        };
        this.updateCounts = function()
        {
            var text = OJE.element.text();

            var words = $.trim(text).split(" ");

            $("#wordCount").html(words.length);
            $("#characterCount").html($.trim(text).length);
        };
        this.cleanTags = function()
        {
            var _this = this;
            OJE.element.find("*").each(function()
            {
                $(this).removeAttr("style");
                var tagName = $(this).prop("tagName").toLocaleLowerCase();
                if (OJE.tagWhiteList.indexOf(tagName) < 0)
                {
                    $(this).contents().unwrap();
                }
            });
        };

    };

    this.parseHash = function()
    {
        if (location.hash)
        {
            var hash = location.hash.replace('#', '');

            if (hash.length > 3)
            {
                OJE.Note.loadNote(hash);
                return true;
            }
        }
        return false;

    };
    this.showMessage = function(message, timeout, isNotError)
    {
        timeout = timeout || 10000;

        var messageClass = isNotError ? "messages" : "errors";
        var messageArray = isNotError ? this.messages : this.errors;

        var time = new Date().getTime();

        messageArray.push(message);

        $("#" + messageClass).show();
        $("#" + messageClass).append('<p id="message-' + time + '">' + message + '</p>')

        var _this = this;
        setTimeout(function()
        {

            messageArray.pop();

            $("#message-" + time).remove();

            if (messageArray.length < 1)
            {
                $("#" + messageClass).hide();
            }

        }, timeout);
    };

    /**
     * Return true if no error, false if has error.
     * @param response
     * @returns {boolean}
     */
    this.validateAjaxError = function(response)
    {
        if (response && response.hasError)
        {
            this.showMessage(response.errorMessage + " Error Code #" + response.errorCode);
            return false;
        }

        return true;
    };
    this.view = function(elementId, elementTitleId, noteId)
    {
        this.element = $("#" + elementId);
        this.elementTitle = $("#" + elementTitleId);
        this.Note.noteId = noteId;

        $("#noteSearch").keyup(OJE.noteSearch);
    };
    this.initialize = function(elementId, titleId, placeholderText, placeholderTitle)
    {
        this.placeholderText = placeholderText;
        this.placeholderTitle = placeholderTitle;

        this.element = $("#" + elementId);
        this.elementTitle = $("#" + titleId);

        this.element.keydown(this.elementKeyDown);
        this.element.keyup(this.elementKeyUp);
        this.element.blur(this.elementBlur);
        this.element.click(this.elementClick);

        this.elementTitle.keydown(this.elementTitleKeyDown);
        this.elementTitle.keyup(this.elementTitleKeyUp);
        this.elementTitle.blur(this.elementTitleBlur);
        this.elementTitle.click(this.elementTitleClick);

        var _this = this;
        this.element.bind('paste', function(e)
        {
            setTimeout(function()
            {
                OJE.Note.markAsDirty();
                OJE.Note.cleanTags();
            }, 1);
        });

        if (!OJE.parseHash())
        {
            this.element.html(this.placeholderText);
            this.elementTitle.html(this.placeholderTitle);

            if (OJE.loggedIn)
            {
                this.Note.Save.triggerAutoSave();
            }
        }

        $(window).bind('beforeunload', function()
        {

            var e = e || window.event,
                message = 'You have unsaved changes, if you leave these unsaved changes will be lost.';

            if (OJE.loggedIn && OJE.Note.Save.shouldTriggerAutoSave())
            {
                // For IE and Firefox prior to version 4
                if (e)
                {
                    e.returnValue = message;
                }

                return message;
            }
            else
            {
                return;
            }
        });

        $("#noteSearch").on('input', OJE.noteSearch);

    };

    this.noteTimeout = null;

    this.noteSearch = function()
    {
        if (OJE.noteTimeout)
        {
            clearTimeout(OJE.noteTimeout);
        }

        var searchKey = $("#noteSearch").val();

        if (searchKey.length < 2)
        {
            searchKey = "";
        }
        OJE.noteTimeout = setTimeout(function()
        {
            OJE.noteTimeout = null;
            OJE.Note.currentSearchKey = searchKey;
            OJE.Note.fetchNotes(1, OJE.edit, searchKey);

        }, 400);

    };
    this.forceCursorToStart = function(e, domTarget)
    {
        e.preventDefault();
        domTarget.focus();

        if (window.getSelection && document.createRange)
        {
            // IE 9 and non-IE
            var sel = window.getSelection();
            var range = document.createRange();
            range.setStart(domTarget, 0);
            range.collapse(true);
            sel.removeAllRanges();
            sel.addRange(range);
        }
        else if (document.body.createTextRange)
        {
            // IE < 9
            var textRange = document.body.createTextRange();
            textRange.moveToElementText(domTarget);
            textRange.collapse(true);
            textRange.select();
        }

    };
    this.getTitleText = function()
    {
        return $.trim(OJE.elementTitle.text());
    };
    this.getText = function()
    {
        return OJE.element.html();
    };
    this.refreshEditState = function()
    {
        ///TITLE
        var titleText = OJE.getTitleText();
        var titleHasPlaceholder = OJE.elementTitle.find("#placeholderEmptyTitleTextId").length > 0;
        if (titleHasPlaceholder && OJE.elementTitle.is(":focus"))
        {
            OJE.elementTitle.addClass("editable-title-initial");
        }
        else if (titleHasPlaceholder && OJE.loggedIn)
        {
            OJE.elementTitle.addClass("editable-title-initial");
        }
        else if (titleHasPlaceholder)
        {
            OJE.elementTitle.removeClass("editable-title-initial");
        }
        else
        {
            OJE.elementTitle.removeClass("editable-title-initial");
        }

        //TEXT
        var text = OJE.getText();
        if (OJE.element.find("#placeholderEmptyTextId").length > 0)
        {
            OJE.element.addClass("editable-initial");
        }
        else
        {
            OJE.element.removeClass("editable-initial");
        }

    };
    this.elementTitleClick = function(e)
    {
        if (OJE.element.find("#placeholderEmptyTextId").length > 0)
        {
            OJE.forceCursorToStart(e, OJE.elementTitle[0]);
        }

        OJE.refreshEditState();

    };
    this.elementTitleKeyDown = function(e)
    {
        if (e.which == 13)
        {
            //Block enter key
            return false;
        }

        if (OJE.elementTitle.find("#placeholderEmptyTitleTextId").length > 0)
        {
            //Clear out placeholder or empty title
            OJE.elementTitle.html("");
        }

        OJE.refreshEditState();
    };
    this.elementTitleKeyUp = function()
    {
        if (!OJE.getTitleText())
        {
            //Empty title
            OJE.elementTitle.html(OJE.placeholderTitle);
        }

        OJE.refreshEditState();
        OJE.Note.markAsDirty();
    };
    this.elementTitleBlur = function()
    {
        if (!OJE.getTitleText())
        {
            OJE.elementTitle.html(OJE.placeholderTitle);
        }
        OJE.refreshEditState();
    };
    this.elementClick = function(e)
    {
        if (OJE.element.find("#placeholderEmptyTextId").length > 0)
        {
            OJE.forceCursorToStart(e, OJE.element[0]);
        }

        OJE.refreshEditState();

    };
    this.elementBlur = function()
    {
        var text = OJE.element.text();
        if (!text)
        {
            OJE.element.html(OJE.placeholderText);
        }
        OJE.refreshEditState();
    };
    this.elementKeyUp = function()
    {
        var text = OJE.element.text();
        if (!text)
        {
            OJE.element.html(OJE.placeholderText);
        }
        OJE.refreshEditState();
        OJE.Note.updateCounts();
        OJE.Note.markAsDirty();
    };
    this.elementKeyDown = function()
    {
        if (OJE.element.find("#placeholderEmptyTextId").length > 0)
        {
            OJE.element.html("");
        }

        OJE.refreshEditState();
    };

    this.refreshSettings = function()
    {
        var fontSize = $("#fontSize").val();
        OJE.element.css("font-size", fontSize);

    };
    this.toggleMenu = function(show)
    {
        if (show)
        {
            $("#menuContent").show();
            $("#menuNavigation").show();
            $("#menu").addClass("menu-visible");

            var contentOffset = $("#wrapper").offset();

            if (contentOffset.left < 200)
            {
                $("#wrapper").css("margin-left", "210px");
            }
        }
        else
        {
            $("#menuContent").hide();
            $("#menuNavigation").hide();
            $("#menu").removeClass("menu-visible");
            $("#wrapper").css("margin-left", "auto");
        }
    };
    this.setupMenu = function()
    {

        var menu = $("#menu");
        var menuContent = $("#menuContent");

        var left = $("#wrapper").offset().left;
        if (left + 40 > menu.width())
        {
            left = menu.width();
        }
        else
        {
            left = left + 40;
        }

        var menuHover = $('<div class="menu-hover" id="menuHover">&nbsp;</div>');
        menuHover.css({width: left});
        $('body').append(menuHover);

        $("#menuIcon").click(function()
        {
            if (OJE.menuStuck)
            {
                OJE.toggleMenu(false);
                OJE.menuStuck = false;
            }
            else
            {
                OJE.toggleMenu(true);
                OJE.menuStuck = true;
            }
        });
        menu.mouseleave(function()
        {
            if (!OJE.menuStuck)
            {
                OJE.toggleMenu(false);
            }
        });

        $('body').mousemove(function(e)
        {
            if (!OJE.menuStuck)
            {
                var mouseX = e.pageX;
                var menuHoverX = menuHover.width();

                if (menuHoverX > mouseX && !menuContent.is(":visible"))
                {
                    OJE.toggleMenu(true);
                }
            }

        });


    };
    this.changeUrl = function(url)
    {
        window.location = url;
    };
    this.checkForMobile = function()
    {
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent))
        {
            OJE.showMessage("Hello Mobile User! We are still in beta and mobile support is limited.", 45000, true);
        }
    };
    this.attachListeners = function()
    {
        $("#loginForm").submit(OJE.Authenticate.validateAndProcessLogin);
        $("#registerForm").submit(OJE.Authenticate.validateAndProcessRegister);
    };

};




