<template>
    <div :id="'reply-'+id" class="panel panel-default">
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profiles/'+data.owner.name" v-text="data.owner.name">
                    </a> said <span v-text="ago"></span> 
                </h5>

                <!--@if (Auth::check())
                <div>
                <favorite :reply="{{ $reply }}"></favorite>
                    {{--<form method="POST" action="/replies/{{ $reply->id }}/favorites">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-default" {{ $reply->isFavorited() ? 'disabled' : "" }}>
                            {{ $reply->favorites_count }} {{ str_plural('Favorite', $reply->favorites_count) }}
                        </button>
                    </form>--}}
                </div>
                @endif-->

                <div v-if="signedIn">
                <favorite :reply="data"></favorite>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div v-if="editing">
                <textarea class="form-control" v-model="body"></textarea>
                <button class="btn btn-xs btn-link" @click="update">Update</button>
                <button class="btn btn-xs btn-link" @click="editing = false">Cancel</button>
            </div>
            <div v-else v-text="body">
                <!--{{--<div class="body">{{ $reply->body }}</div>--}}-->
            </div>

        </div>

        <!--@can('update', $reply)-->

        <div class="panel-footer level" v-if="canUpdate">
            <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>
            <button class="btn btn-xs btn-danger mr-1" @click="destroy">Delete</button>
            <!--<form method="POST" action="/replies/{{$reply->id}}">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="submit" class="btn btn-danger btn-xs">Delete</button>
            </form>-->
        </div>

        <!--@endcan-->
    </div>
</template>

<script>
import Favorite from './Favorite.vue';
import moment from 'moment';

    export default {
        props: ['data'],

        components: { Favorite }, 

        data() {
            return {
                editing: false,
                id: this.data.id,
                body: this.data.body
            };
        },

        computed: {
            ago() {
                return moment.utc(this.data.created_at).fromNow() + '...';
            },
            
            signedIn () {
                return window.App.signedIn
            },

            canUpdate() {
                return this.authorize(user => this.data.user_id == user.id);
                //this takes the data.user_id, and matches with w/ user.id
                //return data.user_id == window.App.user.id;
            }
        },

        methods: {
            update() {
                axios.patch('/replies/' + this.data.id, {
                    body: this.body
                });
        //whats up with this update method
                this.editing = false;

                flash('Updated!');
            },

            destroy() {
                axios.delete('/replies/' + this.data.id);
                this.$emit('deleted', this.data.id);
                //when deleted... what is going to do on the parent?
                //an announcement of deletion
                //$(this.$el).fadeOut(300, () => {
                //flash('Your reply has been deleted');
                //});  
            }
        }
    }
</script>