'use strict';

(function() {
    function toggleGuide(shepherd) {
        window.setQuixSession({'key': 'guide-quix', 'value': 'hide'});
    }

    function init() {
        var shepherd = setupShepherd();
        setTimeout(function() {
            shepherd.start();
        }, 400);
    }

    function setupShepherd() {
        var shepherd = new Shepherd.Tour({
            classPrefix: 'quix-tour-',
            defaultStepOptions: {
                cancelIcon: {
                    enabled: true,
                },
                classes: 'class-1 class-2',
                scrollTo: {
                    behavior: 'smooth',
                    block: 'center',
                },
            },
            // This should add the first tour step
            steps: [
                {
                    text: '\n<h3>Welcome to Quix 4</h3><p>Explore the all-new feature of Quix.</p>\n',
                    attachTo: {
                        element: '#qx-welcome-v3-wrapper',
                        on: 'bottom',
                    },
                    buttons: [
                        {
                            action: function() {
                                return this.cancel();
                            },
                            secondary: true,
                            text: 'Exit',
                        },
                        {
                            action: function() {
                                return this.next();
                            },
                            text: 'Next',
                        },
                    ],
                    id: 'welcome',
                },
            ],
            useModalOverlay: true,
        });

        // These steps should be added via `addSteps`
        const steps = [
            {
                title: 'Navigation',
                text: `\n<p>Easily navigate through all content type.</p>\n`,
                attachTo: {
                    element: '.qx-container.qx-navbar .qx-navbar-left',
                    on: 'bottom',
                },
                buttons: [
                    {
                        action: function() {
                            return this.back();
                        },
                        secondary: true,
                        text: 'Back',
                    },
                    {
                        action: function() {
                            return this.next();
                        },
                        text: 'Next',
                    },
                ],
                id: 'including',
            },
            {
                title: 'License',
                text: `\n<p>Activate your Quix PRO license and get all the amazing features.</p>\n`,
                attachTo: {
                    element: '#license-activation-cta',
                    on: 'bottom',
                },
                buttons: [
                    {
                        action: function() {
                            return this.back();
                        },
                        secondary: true,
                        text: 'Back',
                    },
                    {
                        action: function() {
                            return this.next();
                        },
                        text: 'Next',
                    },
                ],
                id: 'including',
            },
            {
                title: 'Settings',
                text: `\n<p>All the settings you needed reside here.</p>\n`,
                attachTo: {
                    element: '#toolbar-settings-right',
                    on: 'bottom',
                },
                buttons: [
                    {
                        action: function() {
                            return this.back();
                        },
                        secondary: true,
                        text: 'Back',
                    },
                    {
                        action: function() {
                            return this.next();
                        },
                        text: 'Next',
                    },
                ],
                id: 'including',
            }
        ];

        shepherd.cancel = function() {
            window.setQuixSession({'key': 'guide-quix', 'value': 'hide'});
            this._done();
        };
        shepherd.complete = function() {
            window.setComponentParams({'key': 'guide-quix', 'value': 'hide'});
            this._done();
        };

        shepherd.addSteps(steps);

        shepherd.addStep({
            title: 'Create new page',
            text: 'Build your page with Quix 4',
            attachTo: {
                element: '#js-new-page-prompt',
                on: 'top',
            },
            buttons: [
                {
                    action: function() {
                        return this.back();
                    },
                    secondary: true,
                    text: 'Back',
                },
                {
                    action: function() {
                        return this.next();
                    },
                    text: 'Done',
                },
            ],
            id: 'followup',
            modalOverlayOpeningPadding: '10',
        });

        return shepherd;
    }

    function ready() {
        if (document.attachEvent ? document.readyState === 'complete' : document.readyState !== 'loading') {
            init();
        }
        else {
            document.addEventListener('DOMContentLoaded', init);
        }
    }

    ready();
}).call(void 0);
