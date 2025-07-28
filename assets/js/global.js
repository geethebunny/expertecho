jQuery(function ($) {
    $(document).ready(function () {
        // * Create Lucide icons
        lucide.createIcons();

        // * Prevent Form submit
        $(".form-no-submit").on("submit", function (e) {
            e.preventDefault();
        });

        // ! ======================================
        // ! ----------- STICKY HEADER ------------
        // ! ======================================

        // * Function to update sticky header
        function updateStickyHeader() {
            if ($(window).scrollTop() >= 1) $(".site-header").addClass("sticky");
            else $(".site-header").removeClass("sticky");
        }

        // * Run on page load
        updateStickyHeader();

        // * Run on page scroll
        $(window).on("scroll", updateStickyHeader);

        // ! ======================================
        // ! ----------- HEADER SEARCH ------------
        // ! ======================================

        // * Header search timer
        var header_search_timer;
        $(".header-search-input:visible").on("input", function (e) {
            const value = $(this).val().trim();
            if (value.length >= 3) {
                clearTimeout(header_search_timer);
                header_search_timer = setTimeout(header_search_finish, 1000);
            } else {
                $(".header-search").addClass("hidden");
            }
        });

        // * Header search function
        function header_search_finish() {
            const s_name = $(".header-search-input:visible").val().trim();
            const $headerSearch = $(".header-search");

            $.ajax({
                url: local_scripts.ajaxurl,
                type: "GET",
                data: {
                    action: "header_search",
                    s_name: s_name,
                },
                dataType: "json",
                beforeSend: function () {
                    // Show
                    $headerSearch.removeClass("hidden");

                    // Show spinner
                    $headerSearch.find(".spinner").removeClass("hidden");

                    // Clear list
                    $headerSearch.find(".results").empty();

                    // Hide no results
                    $headerSearch.find(".empty-results").addClass("hidden");
                },
                complete: function () {
                    // Hide spinner
                    $headerSearch.find(".spinner").addClass("hidden");

                    // Lucide
                    lucide.createIcons();
                },
                success: function (data, status, xhr) {
                    if (data) $headerSearch.find(".results").append(data);
                    else $headerSearch.find(".empty-results").removeClass("hidden");
                },
                error: function (jqXhr, textStatus, errorMessage) {
                    console.error("Header search error:", errorMessage);
                },
            });
        }

        // TODO: ENQUIRE
        // Enquire Search START
        var expert_search_predict_timer;

        $("#experts-text").on("input", function (e) {
            if ($(this).val().length >= 3) {
                clearTimeout(expert_search_predict_timer);
                expert_search_predict_timer = setTimeout(expert_search_predict_finish, 1000);
            } else {
                $("#enquire-experts-search").addClass("hidden");
            }
        });

        function expert_search_predict_finish() {
            $.ajax({
                url: local_scripts.ajaxurl,
                type: "get",
                data: {
                    action: "expert_spearch_prediction",
                    s_name: $("#experts-text").val(),
                },
                dataType: "json",
                beforeSend: function () {
                    // Show
                    $("#enquire-experts-search").removeClass("hidden");

                    // Show spinner
                    $("#enquire-experts-search .spinner").removeClass("hidden");

                    // Clear list
                    $("#enquire-experts-prediction").empty();

                    // Hide no results
                    $("#enquire-experts-search .empty-results").addClass("hidden");
                },
                complete: function () {
                    // Hide spinner
                    $("#enquire-experts-search .spinner").addClass("hidden");

                    // Lucide
                    lucide.createIcons();
                },
                success: function (data, status, xhr) {
                    if (data != "") $("#enquire-experts-prediction").append(data);
                    else $("#enquire-experts-search .empty-results").removeClass("hidden");
                },
                error: function (jqXhr, textStatus, errorMessage) {},
            });
        }

        // Expert Enquiry Click
        $("#enquire-experts-prediction").on("click", ".enquire-expert", function () {
            var id = String($(this).data("expert-id"));
            var cookie_experts = Cookies.get("experts");

            // Expert exists
            if (cookie_experts) {
                cookie_experts = cookie_experts.split("|");

                // Check if already exists
                if (!cookie_experts.includes(id)) cookie_experts.push(id);
                else return;
            } else {
                cookie_experts = [id];
            }

            // Join
            cookie_experts = cookie_experts.join("|");

            Cookies.set("experts", cookie_experts);

            // Remove input
            $("#experts-text").val("");

            // Update hidden value
            if ($("#experts-hiddenlist").val())
                $("#experts-hiddenlist").val($("#experts-hiddenlist").val() + "\r\n" + $(this).data("experts-name"));
            else $("#experts-hiddenlist").val($(this).data("expert-name"));

            // Hide search
            $("#enquire-experts-search").addClass("hidden");

            // Append
            $("#enquire-experts-list").append($(this));
        });

        // Expert Enquiry Remove
        $("#enquire-experts-list").on("click", "i", function () {
            // Init
            var id = String($(this).parent().parent().data("expert-id"));
            var name = $(this).parent().parent().data("expert-name");
            var cookie_experts = Cookies.get("experts").split("|");
            var experts_hidden_list = $("#expert-hiddenlist").val();

            // Check if not last element
            if (experts_hidden_list.includes(", ")) {
                // Update hidden value
                $("#experts-hiddenlist").val(experts_hidden_list.replace(name + ", ", "").replace(", " + name, ""));

                // Update cookies
                cookie_experts = cookie_experts.filter(function (e) {
                    return e !== id;
                });

                Cookies.set("experts", cookie_experts.join("|"));
            } else {
                // Remove hidden value
                $("#experts-hiddenlist").val("");

                // Remove cookies
                Cookies.remove("experts");
            }

            // Remove element
            $(this).parent().parent().slideUp();
        });

        // Page - Expert Enquiry
        if ($("body").hasClass("page-template-template_enquiries")) {
            // Load experts
            $("#enquire-experts-list").append($("#cookie-experts .enquire-expert"));

            // Set form value
            $("#experts-hiddenlist").val($("#cookie-experts-form-input").data("value"));
        }

        // ! ======================================
        // ! ----------- EXPERTS SEARCH -----------
        // ! ======================================

        let main_search_timer;
        let current_page = 1;
        let is_pagination = false;

        // * Function to trigger Main Search
        function triggerMainSearch(time = 1000) {
            clearTimeout(main_search_timer);
            main_search_timer = setTimeout(main_search_timer_finish, time);
        }

        // * Trigger on input change
        $("#main-search").on("input", () => triggerMainSearch(1000));

        // * Trigger on category change
        $(".experts-filter-row input[type=checkbox]").on("change", () => triggerMainSearch(1000));

        // * Pagination
        $(".experts-pagination").on("click", "a", function (e) {
            e.preventDefault();

            // Set page
            if ($(this).html() === "&lt;") current_page--;
            else if ($(this).html() === "&gt;") current_page++;
            else current_page = parseInt($(this).text().trim());

            // Page limits
            current_page = Math.max(1, current_page);

            // Set pagination
            is_pagination = true;

            // Run search instantly
            triggerMainSearch(0);

            // Scroll
            $([document.documentElement, document.body]).animate(
                {
                    scrollTop: $(".main-results").offset().top - 150,
                },
                50
            );
        });

        // * Main search function
        function main_search_timer_finish() {
            var url = window.location.href.split("?")[0];
            const $searchPageContent = $(".search-page-content");
            var categories = [];

            $('.filter-row input[data-tier="2"]:checked').each(function () {
                categories.push($(this).val());
            });

            $.ajax({
                url: local_scripts.ajaxurl,
                type: "get",
                data: {
                    action: "main_search_experts",
                    search: $("#main-search").val(),
                    categories: categories.join("|"),
                    page: current_page,
                },
                dataType: "json",
                beforeSend: function () {
                    // Show spinner
                    $searchPageContent.find(".spinner").removeClass("hidden");

                    // Clear list
                    $searchPageContent.find(".main-results").empty();

                    // Clear Paginatiion
                    if (!is_pagination) {
                        current_page = 1;
                    }
                    $searchPageContent.find(".pagination").empty();

                    // Hide no results
                    $searchPageContent.find(".empty-results").addClass("hidden");

                    // Clear URL
                    window.history.pushState("Experts Search", "Experts", url);
                },
                complete: function () {
                    // Hide spinner
                    $searchPageContent.find(".spinner").addClass("hidden");

                    // Lucide
                    lucide.createIcons();

                    // Reset page
                    is_pagination = false;
                },
                success: function (data, status, xhr) {
                    if (data.experts != "") {
                        $searchPageContent.find(".main-results").append(data.experts);
                        $searchPageContent.find(".pagination").append(data.pagination);
                    } else {
                        $searchPageContent.find(".empty-results").removeClass("hidden");
                        console.log("EMPTY");
                    }
                },
                error: function (jqXhr, textStatus, errorMessage) {
                    console.error("AJAX Error:", textStatus, errorMessage);
                },
            });
        }

        // ! ======================================
        // ! --------------- MOBILE ---------------
        // ! ======================================

        $(".mobile-toggle").on("click", function (e) {
            if ($(this).hasClass("active")) {
                $(this).siblings(".left").slideUp();
            } else {
                $(this).siblings(".left").slideDown();
            }
            $(this).toggleClass("active");
        });

        // ! ======================================
        // ! -------------- FLICKITY --------------
        // ! ======================================

        $(".expert-media-list").flickity({
            contain: true,
            pageDots: false,
            adaptiiveHeight: true,
            wrapAround: true,
            watchCSS: true,
        });

        // ! ======================================
        // ! -------------- SHORTLIST -------------
        // ! ======================================

        // * Function to change shortlist count
        function updateShortlistCount(delta) {
            const $shortlist = $(".shortlist");
            const $count = $(".shortlist-count");

            // Remove animation
            $shortlist.removeClass("pulse");

            // Force restart animation
            void $shortlist[0].offsetWidth;

            // Add animation
            $shortlist.addClass("pulse");

            // Remove the class after animation ends so it can re-trigger
            $shortlist.one("animationend", () => {
                $shortlist.removeClass("pulse");
            });

            $count.text((parseInt($count.text(), 10) || 0) + delta);
        }

        // * Function to add a toast message
        function showToast(message) {
            const $container = $(".toast-container");
            const $existing_toasts = $container.children(".toast");

            // Animate existing toasts up
            $existing_toasts.each(function () {
                const currentY = parseFloat($(this).css("--shift-y") || 0);
                const newY = currentY - 50; // Adjust depending on your toast height
                $(this).css("--shift-y", `${newY}px`);
                $(this).css("transform", `translateY(${newY}px)`);
            });

            // Create toast
            const $toast = $(`<div class="toast">${message}</div>`);
            $toast.css({
                opacity: 0,
                transform: "translateY(50px)",
            });
            $container.append($toast);

            // Trigger reflow, then animate into place
            $toast[0].offsetHeight;
            $toast.css({
                opacity: 1,
                transform: "translateY(0px)",
            });

            // Remove toast after animation ends (3s)
            setTimeout(() => {
                $toast.css("opacity", 0);
                setTimeout(() => $toast.remove(), 600);
            }, 5000);
        }

        // * Adds expert to the shortlist
        $(".main-results").on("click", ".add-button", function (e) {
            e.preventDefault();

            // Init
            const $button = $(this);
            const id = String($(this).parent().data("expert-id"));
            const is_adding = $button.find(".icon--x").hasClass("hidden");

            // Cookie
            let cookie_experts = Cookies.get("experts");
            let expert_list = cookie_experts ? cookie_experts.split("|") : [];

            // Update cookies
            if (is_adding) {
                // Add if it doesn't exist
                if (!expert_list.includes(id)) {
                    expert_list.push(id);
                    Cookies.set("experts", expert_list.join("|"));
                }

                // Update UI
                $button.find(".icon--plus").addClass("hidden");
                $button.find(".icon--x").removeClass("hidden");
                $button.addClass("active");
                updateShortlistCount(1);
            } else {
                // Remove expert
                expert_list = expert_list.filter((e) => e !== id);

                // Check if not last element
                if (expert_list.length > 0) {
                    Cookies.set("experts", expert_list.join("|"));
                } else {
                    Cookies.remove("experts");
                }

                // Update UI
                $button.find(".icon--plus").removeClass("hidden");
                $button.find(".icon--x").addClass("hidden");
                $button.removeClass("active");
                updateShortlistCount(-1);
            }

            // Show toast
            showToast(is_adding ? "Expert added to shortlist." : "Expert removed from shortlist.");
        });

        // * Removes expert from the shortlist
        $(".shortlist-list .remove-button").on("click", function (e) {
            e.preventDefault();

            // Init
            var id = String($(this).parent().data("expert-id"));
            var cookie_experts = Cookies.get("experts").split("|");

            // Check if not last element
            if (cookie_experts.length > 1) {
                // Update cookies
                cookie_experts = cookie_experts.filter(function (e) {
                    return e !== id;
                });

                Cookies.set("experts", cookie_experts.join("|"));
            } else {
                // Remove cookies
                Cookies.remove("experts");
            }

            // Reload Page
            window.location.href = $(this).data("url");
        });

        // Expert Talking Point Toggle
        $(".talking-point .title").on("click", function (e) {
            if ($(this).hasClass("active")) {
                // $(this).find('.fa-chevron-up')
            }

            $(this).find(".fa-chevron-up").toggleClass("hidden");
            $(this).find(".fa-chevron-down").toggleClass("hidden");
            $(this).siblings(".text").toggleClass("hidden");

            $(this).toggleClass("active");
        });
    });
});
