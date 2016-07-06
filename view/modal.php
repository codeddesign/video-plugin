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
                <label for="ad3media_campaign">
                    Campaign name
                </label>
                <input class="field" id="ad3media_campaign" placeholder="My campaign.." type="text" v-model="campaign.campaign_name">
            </div>

            <div v-if="!code">
                <label for="ad3media_yt">
                    Youtube link
                </label>
                <input class="field" id="ad3media_yt" placeholder="https://www.youtube.com/watch?v=12345678900" type="text" v-model="campaign.video_url">
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

<script src="http://cdnjs.cloudflare.com/ajax/libs/vue/1.0.25/vue.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/0.8.0/vue-resource.js"></script>
<script type="text/javascript">
    (function($) {
        var $ad3 = $('#ad3media__move'),
            $clone = $ad3.clone();

        $clone.appendTo('body');

        $ad3.remove();
    })(jQuery || $);

    new Vue({
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

                this.$http.get('<?=$config['app_host']?>/plugin/campaign-add', this.campaign)
                    .then(function(response) {
                        if(response.data.error) {
                            this.error = response.data.error;

                            return false;
                        }

                        this.error = 'Copy the code into your post or click Insert';

                        this.code = '[ad3media id="' + response.data.play_id + '"]';
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
            }
        }
    })
</script>
