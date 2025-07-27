jQuery(function ($) {
    $(document).ready(function () {
        // Lucide
        lucide.createIcons();

        // Sticky header
        if ($(window).scrollTop() >= 1) $(".site-header").addClass("sticky");

        $(window).scroll(function () {
            var scroll = $(window).scrollTop();
            if (scroll >= 1) $(".site-header").addClass("sticky");
            else $(".site-header").removeClass("sticky");
        });

        // Header Search START
        var header_search_timer;
        $(".header-search-input").on("input", function (e) {
            if ($(this).val().length >= 3) {
                clearTimeout(header_search_timer);
                header_search_timer = setTimeout(header_search_finish, 1000);
            } else {
                $(".header-search").addClass("hidden");
            }
        });

        function header_search_finish() {
            var s_name = "";
            $(".header-search-input").each(function (i) {
                s_name = s_name + $(this).val();
            });
            $.ajax({
                url: local_scripts.ajaxurl,
                type: "get",
                data: {
                    action: "header_search",
                    s_name: s_name,
                },
                dataType: "json",
                beforeSend: function () {
                    // Show
                    $(".header-search").removeClass("hidden");

                    // Show spinner
                    $(".header-search .spinner").removeClass("hidden");

                    // Clear list
                    $(".header-search .results").empty();

                    // Hide no results
                    $(".header-search .empty-results").addClass("hidden");
                },
                complete: function () {
                    // Hide spinner
                    $(".header-search .spinner").addClass("hidden");

                    // Lucide
                    lucide.createIcons();
                },
                success: function (data, status, xhr) {
                    if (data != "") $(".header-search .results").append(data);
                    else
                        $(".header-search .empty-results").removeClass(
                            "hidden"
                        );
                },
                error: function (jqXhr, textStatus, errorMessage) {},
            });
        }
        // Header Search END

        // Enquire Search START
        var expert_search_predict_timer;

        $("#experts-text").on("input", function (e) {
            if ($(this).val().length >= 3) {
                clearTimeout(expert_search_predict_timer);
                expert_search_predict_timer = setTimeout(
                    expert_search_predict_finish,
                    1000
                );
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
                    $("#enquire-experts-search .empty-results").addClass(
                        "hidden"
                    );
                },
                complete: function () {
                    // Hide spinner
                    $("#enquire-experts-search .spinner").addClass("hidden");

                    // Lucide
                    lucide.createIcons();
                },
                success: function (data, status, xhr) {
                    if (data != "")
                        $("#enquire-experts-prediction").append(data);
                    else
                        $("#enquire-experts-search .empty-results").removeClass(
                            "hidden"
                        );
                },
                error: function (jqXhr, textStatus, errorMessage) {},
            });
        }

        // Expert Enquiry Click
        $("#enquire-experts-prediction").on(
            "click",
            ".enquire-expert",
            function () {
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
                    $("#experts-hiddenlist").val(
                        $("#experts-hiddenlist").val() +
                            "\r\n" +
                            $(this).data("experts-name")
                    );
                else $("#experts-hiddenlist").val($(this).data("expert-name"));

                // Hide search
                $("#enquire-experts-search").addClass("hidden");

                // Append
                $("#enquire-experts-list").append($(this));
            }
        );

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
                $("#experts-hiddenlist").val(
                    experts_hidden_list
                        .replace(name + ", ", "")
                        .replace(", " + name, "")
                );

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
            $("#enquire-experts-list").append(
                $("#cookie-experts .enquire-expert")
            );

            // Set form value
            $("#experts-hiddenlist").val(
                $("#cookie-experts-form-input").data("value")
            );
        }

        // Prevent Form submit
        $(".form-no-submit").on("submit", function (e) {
            e.preventDefault();
        });

        var main_search_timer;
        var current_page = 1;

        $("#main-search").on("input", function (e) {
            var val = $(this).val();

            clearTimeout(main_search_timer);
            main_search_timer = setTimeout(main_search_timer_finish, 1000);
        });

        // Change Category
        $(".experts-filter-row input[type=checkbox]").on(
            "change",
            function (e) {
                var child = $(this).parent().next(".filter-row-child");

                // If parent and closed, deselect all child
                if (child) {
                    if ($(this).prop("checked")) {
                        child.removeClass("hidden");
                    } else {
                        child
                            .find("input[type=checkbox]")
                            .prop("checked", false);
                        child.addClass("hidden");
                    }
                }

                clearTimeout(main_search_timer);
                // main_search_timer = setTimeout( main_search_timer_finish, 1000 );
            }
        );

        // Pagination
        $(".experts-pagination").on("click", "a", function (e) {
            e.preventDefault();

            console.log($(this).html());

            // Set Page
            if ($(this).html() == "&lt;") current_page--;
            else if ($(this).html() == "&gt;") current_page++;
            else current_page = $(this).html();

            // Query
            clearTimeout(main_search_timer);
            main_search_timer = setTimeout(main_search_timer_finish, 0);

            // Scroll
            $([document.documentElement, document.body]).animate(
                {
                    scrollTop: $(".main-results").offset().top - 150,
                },
                50
            );
        });

        function main_search_timer_finish() {
            var url = window.location.href.split("?")[0];
            var t2_boxes = [];
            var t3_boxes = [];

            $('.filter-row input[data-tier="2"]:checked').each(function () {
                t2_boxes.push($(this).val());
            });

            $('.filter-row input[data-tier="3"]:checked').each(function () {
                t3_boxes.push($(this).val());
            });

            $.ajax({
                url: local_scripts.ajaxurl,
                type: "get",
                data: {
                    action: "main_search_experts",
                    search: $("#main-search").val(),
                    t2_categories: t2_boxes.join("|"),
                    t3_categories: t3_boxes.join("|"),
                    page: current_page,
                },
                dataType: "json",
                beforeSend: function () {
                    // Show spinner
                    $(".search-page-content .spinner").removeClass("hidden");

                    // Clear list
                    $(".search-page-content .main-results").empty();

                    // Clear Paginatiion
                    current_page = 1;
                    $(".search-page-content .pagination").empty();

                    // Hide no results
                    $(".search-page-content .empty-results").addClass("hidden");

                    // Clear URL
                    window.history.pushState("Experts Search", "Experts", url);
                },
                complete: function () {
                    // Hide spinner
                    $(".search-page-content .spinner").addClass("hidden");

                    // Lucide
                    lucide.createIcons();
                },
                success: function (data, status, xhr) {
                    if (data != "") {
                        $(".search-page-content .main-results").append(
                            data.experts
                        );
                        $(".search-page-content .pagination").append(
                            data.pagination
                        );
                    } else {
                        $(".search-page-content .empty-results").removeClass(
                            "hidden"
                        );
                    }
                },
                error: function (jqXhr, textStatus, errorMessage) {},
            });
        }

        // Blog Search
        var blog_search_timer;

        $("#blog-search").on("input", function (e) {
            clearTimeout(blog_search_timer);
            blog_search_timer = setTimeout(blog_search_timer_finish, 1000);
        });

        // Category
        $(
            "body.page-template-template-blog .filter-row input[type=checkbox]"
        ).on("change", function (e) {
            clearTimeout(blog_search_timer);
            blog_search_timer = setTimeout(blog_search_timer_finish, 1000);
        });

        function blog_search_timer_finish() {
            var category_boxes = [];

            $(".filter-row input[type=checkbox]:checked").each(function () {
                category_boxes.push($(this).val());
            });

            $.ajax({
                url: local_scripts.ajaxurl,
                type: "get",
                data: {
                    action: "blog_search_experts",
                    search: $("#blog-search").val(),
                    categories: category_boxes.join("|"),
                },
                dataType: "json",
                beforeSend: function () {
                    // Show spinner
                    $(".search-page-content .spinner").removeClass("hidden");

                    // Clear list
                    $(".search-page-content .main-results").empty();

                    // Hide no results
                    $(".search-page-content .empty-results").addClass("hidden");
                },
                complete: function () {
                    // Hide spinner
                    $(".search-page-content .spinner").addClass("hidden");

                    // Lucide
                    lucide.createIcons();
                },
                success: function (data, status, xhr) {
                    if (data != "")
                        $(".search-page-content .main-results").append(data);
                    else
                        $(".search-page-content .empty-results").removeClass(
                            "hidden"
                        );
                },
                error: function (jqXhr, textStatus, errorMessage) {},
            });
        }

        // Toggle Filters
        $(".mobile-toggle").on("click", function (e) {
            if ($(this).hasClass("active")) {
                $(this).siblings(".left").slideUp();
            } else {
                $(this).siblings(".left").slideDown();
            }
            $(this).toggleClass("active");
        });

        // Flickity
        $(".expert-media-list").flickity({
            // options
            contain: true,
            pageDots: false,
            adaptiiveHeight: true,
            wrapAround: true,
            watchCSS: true,
        });

        // Shortlist Add
        $(".main-results").on("click", ".add-button", function (e) {
            e.preventDefault();

            // Init
            var id = String($(this).parent().data("expert-id"));
            var cookie_experts = Cookies.get("experts");

            if (cookie_experts) cookie_experts = cookie_experts.split("|");

            // Update cookies
            if ($(this).children(".fa-times").hasClass("hidden")) {
                if (Array.isArray(cookie_experts))
                    cookie_experts = cookie_experts.filter(function (e) {
                        return e !== id;
                    });
                else cookie_experts = [];

                cookie_experts.push(id);

                Cookies.set("experts", cookie_experts.join("|"));

                $(this).find(".icon--plus").addClass("hidden");
                $(this).find(".icon--x").removeClass("hidden");

                // Update text
                $(".shortlist-footer .number").html(
                    parseInt($(".shortlist-footer .number").html()) + 1
                );
            } else {
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

                $(this).find(".icon--plus").removeClass("hidden");
                $(this).find(".icon--x").addClass("hidden");

                // Update text
                $(".shortlist-footer .number").html(
                    parseInt($(".shortlist-footer .number").html()) - 1
                );
            }
        });

        // Shortlist Remove
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
