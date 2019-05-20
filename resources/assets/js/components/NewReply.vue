<template>
    <div>
        <div v-if="signedIn">
            <div class="form-group">
                <textarea name="body" id="body" class="form-control" placeholder="Have something to say?" v-model="body" required rows="5">
            </textarea>
            </div>
            <button type="submit" class="btn btn-default" @click="addReply">Post</button>
        </div>

        <p class="text-center" v-else>Please <a href="/login">sign in</a> to participate in this
                    discussion
        </p>
    </div>
</template>

<script>
    export default {

        //props: ['endpoint'],

        data() {
            return {
                body: '',
            }
        },

        computed: {
            signedIn() {
                return window.App.signedIn;
            }
        },

        methods: {
            addReply() {
                axios.post(location.pathname + '/replies', {
                        body: this.body
                        //i'm wondering why this doesn't need the much else
                        //i guess we assume the id, thread_id, and user_id already
                        //so if we post to this endpoint, this would mean we still
                        //post to the specific endpoint, as well as use it's function
                        //were basically using a function here to access a route
                        //and then using the data in the body here to send to the server 
                    })
                    .then(({data}) => {
                        this.body = '';

                        flash('Your reply has been posted.');

                        this.$emit('created', data);

                        //which event would be need to fire when we post a reply?
                    });
                // this is ES2015
            }
        }
    }
</script>