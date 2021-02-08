'use strict';

function NewsParser() {

    this.routes = null;
    this.el = {
        'form' : '#parseSettings',
        'sources' : '.source-table',
        'parseBtn' : '.parse-resource',
        'parseStatus' : '.source-parsed-status'
    };

    this.template = {
        'sourceElement': '<tr>\n'
            + '<td>#RESOURCE#</td>\n'
            + '<td class="source-parsed-status">#IS_PARSED#</td>\n'
            + '<td><button data-source="#DATA_SOURCE#" class="parse-resource">Парсить</button></td>\n'
        + '</tr>'
    }

    this.init = function (routes) {

        if (routes === undefined) {
            console.error('Routes are not defined');
            return false;
        }

        this.routes = routes
        this.initHandlers();
    }

    this.initHandlers = function () {
        $(document).on('submit', this.el.form, this.formSubmitHandler.bind(this));
        $(document).on('click', this.el.parseBtn, this.parseSource.bind(this));
        $(window).on('load', this.getSources.bind(this));
    }

    this.formSubmitHandler = function (e) {
        e.preventDefault();

        let path = this.routes['SETTINGS'];
        if (path === undefined) {
            console.error('Settings path are not defined');
            return false;
        }

        let formData = $(e.target).serialize();

        $.ajax({
            method: "POST",
            url: path,
            data: formData,
            success: this.successSettingSaveHandler.bind(this)
        });
    }

    this.successSettingSaveHandler = function (answer) {
        if (answer['result'] === undefined) {
            return false;
        }

        let template = this.template.sourceElement;
        template = template.replace(new RegExp('#RESOURCE#', 'g'), answer['result']['resource']);
        template = template.replace(new RegExp('#DATA_SOURCE#', 'g'), answer['result']['resource']);
        template = template.replace(new RegExp('#IS_PARSED#', 'g'), 'NO');
        $(this.el.sources).append(template);
    }

    this.getSources = function (e) {
        e.preventDefault();

        let path = this.routes['SOURCES'];
        if (path === undefined) {
            console.error('Sources path are not defined');
            return false;
        }

        $.ajax({
            method: "GET",
            url: path,
            success: this.updateSourcesByAjaxData.bind(this)
        });
    }

    this.updateSourcesByAjaxData = function (answer) {
        if (answer['result'] === undefined) {
            return false;
        }

        let that = this;
        $(answer['result']).each(function (key, item) {
            let template = that.template.sourceElement;
            template = template.replace(new RegExp('#RESOURCE#', 'g'), item['NAME']);
            template = template.replace(new RegExp('#DATA_SOURCE#', 'g'), item['NAME']);

            let parsedStatus = 'NO';
            if (item['IS_PARSED']) {
                let sourceLink = that.prepareLinkForSource(item['NAME']);
                parsedStatus = '<a href="'+ sourceLink +'">SHOW</a>';

                let templateNode = $(template);
                templateNode.find(that.el.parseBtn).prop('disabled', true);
                template = templateNode.html();
            }

            template = template.replace(new RegExp('#IS_PARSED#', 'g'), parsedStatus);
            $(that.el.sources).append(template);
        });
    }

    this.parseSource = function (e) {
        e.preventDefault();

        let path = this.routes['PARSE'];
        if (path === undefined) {
            console.error('Parse path are not defined');
            return false;
        }

        let targetElement = e.target;
        let source = $(targetElement).data('source');
        $.ajax({
            method: "POST",
            url: path,
            data: {source: source},
            success: this.updateSourceItemByAjaxData.bind(this, targetElement)
        })
    }

    this.updateSourceItemByAjaxData = function (targetElement, answer) {
        if (answer['result'] === undefined || answer['result']['success'] === false) {
            return false;
        }

        let sourceLink = this.prepareLinkForSource($(targetElement).data('source'));
        $(targetElement).parents('tr').find(this.el.parseStatus).html('<a href="'+ sourceLink +'">SHOW</a>');
        $(targetElement).prop('disabled', true);
    }

    this.prepareLinkForSource = function (source) {
        return '/list/?source=' + source;
    }
}