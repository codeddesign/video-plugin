<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.js"></script>

<div class="ad3media__plugin" id="ad3media__move" v-cloak="">
    <div class="ad3media__overlay" v-if="showingModal" v-on:click="toggleModal"></div>
    <div class="ad3media__modal" v-if="showingModal">
        <div class="title">
            Insert video
        </div>

        <div class="body">
            <div class="error" v-if="error">
                {{ error }}
            </div>

            <div v-if="!code">
                <label for="ad3media_yt">
                    Youtube link
                </label>
                <input class="field" id="ad3media_yt" placeholder="https://www.youtube.com/watch?v=12345678900" type="text" v-model="campaign.video_url" v-on:keyup="checkUrl | debounce 1000">
                <span class="status-icon"><i class="fa"></i></span>
            </div>

            <div v-if="!code">
                <label for="ad3media_campaign">
                    Campaign name
                </label>
                <input class="field" id="ad3media_campaign" placeholder="My campaign.." type="text" v-model="campaign.campaign_name">
            </div>

            <div v-if="code">
                <textarea style="width: 100%;resize: none;" v-el:the-code @click="selectCode">{{ code }}</textarea>
            </div>
        </div>

        <div class="footer" v-if="!code">
            <div>
                <a class="button cancel" href="#" v-on:click.prevent.stop="toggleModal">
                    Cancel
                </a>
            </div>
            <div style="float: right;">
                <a class="button button-primary" href="#" v-on:click.prevent.stop="addCampaign">
                    Add
                </a>
            </div>
        </div>

        <div class="footer" v-if="code">
            <div>
                <a class="button cancel" href="#" v-on:click.prevent.stop="newCampaign">
                    New
                </a>
            </div>
            <div style="float: right;">
                <a class="button button-primary" href="#" v-on:click.prevent.stop="insertCode">
                    Insert
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/vue/1.0.25/vue.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/0.8.0/vue-resource.js"></script>
<script type="text/javascript">
    (function($) {
        var $ad3 = $('#ad3media__move'),
            $clone = $ad3.clone();

        $clone.appendTo('body');

        $ad3.remove();

    })(jQuery || $);

    function getYouTubeID(url) {
        var ID = '';
        url = url.replace(/(>|<)/gi,'').split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/);
        if(url[2] !== undefined) {
            ID = url[2].split(/[^0-9a-z_\-]/i);
            ID = ID[0];
        } else {
            ID = url;
        }
        return ID;
    }


    var vm = new Vue({
        el: 'body',

        data: {
            code: false,
            error: false,
            showingModal: false,
            campaign: {
                campaign_name: '',
                video_url: ''
            }
        },

        created: function() {},

        methods: {
            toggleModal: function() {
                this.error = false;
                this.showingModal = !this.showingModal;
            },

            selectCode: function() {
                this.$els.theCode.select();
            },

            addCampaign: function() {
                this.error = false;
                this.code = false;

                if (!this.campaign.campaign_name || !this.campaign.video_url) {
                    this.error = 'All fields are required';
                    return false;
                }

                this.error = 'Please wait..';

                this.$http.get('<?=$config['app_host'];?>/plugin/campaign-add', this.campaign)
                    .then(function(response) {
                        if (response.data.error) {
                            this.error = response.data.error;

                            return false;
                        }

                        this.error = 'Copy the code into your post or click Insert';

                        this.code = '[ad3media campaign="' + response.data.campaign + '" youtube="' + response.data.youtube + '"]';
                    });
            },

            newCampaign: function() {
                this.code = false;
                this.error = false;

                this.campaign.campaign_name = '';
                this.campaign.video_url = '';
            },

            insertCode: function() {
                document.getElementById('content-html').click();

                QTags.insertContent("\n" + this.code + "\n");

                this.showingModal = false;
            },

            checkUrl: function() {
                var Id = getYouTubeID(this.campaign.video_url);
                var ytKey = 'AIzaSyAIjv2KfeLl3IFlS841O8ctyDKytX0togE';
                if(typeof Id == "string") {
                    $('.status-icon i').removeClass('fa-check').removeClass('fa-times').addClass('fa-spinner fa-pulse');
                    $('.status-icon').removeClass('red').removeClass('green').addClass('blue');
                    $.get('https://www.googleapis.com/youtube/v3/videos?id=' + Id + '&key='+ ytKey +'&part=snippet,contentDetails,statistics,status')
                        .done(function(data) {
                            if(data.items.length) {
                                $('.status-icon i').removeClass('fa-times').removeClass('fa-spinner fa-pulse').addClass('fa-check');
                                $('.status-icon').removeClass('blue').removeClass('red').addClass('green');
                                vm.campaign.campaign_name = data.items[0].snippet.localized.title;
                            } else {
                                $('.status-icon i').removeClass('fa-check').removeClass('fa-spinner fa-pulse').addClass('fa-times');
                                $('.status-icon').removeClass('blue').removeClass('green').addClass('red');
                                vm.campaign.campaign_name = '';
                            }
                        });
                } else {
                    $('.status-icon i').removeClass('fa-check').removeClass('fa-spinner fa-pulse').addClass('fa-times');
                    $('.status-icon').removeClass('blue').removeClass('green').addClass('red');
                    vm.campaign.campaign_name = '';
                }
            }
        }
    });

</script>
