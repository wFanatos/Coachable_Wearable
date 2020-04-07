<template>
    <html>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="accordion" id="accordionExample">
                        <div class="card">
                            <div class="card-header" id="headingOne">
                                <h2 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="headingTwo">
                                    Collapsible Group Item #1
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                <div class="accordion" id="accordianCollapse">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">RunID</th>
                                                <th scope="col">Speed</th>
                                                <th scope="col">Distance</th>
                                            </tr>
                                        </thead>
                                        <div class="card">
                                            <!-- <div class="card-header" id="headingTwo"> -->
                                                <!-- <h2 class="mb-0"> -->
                                                                                       
                                                    <tr v-for="data in athletedata" v-bind:key="data">
                                                        <button class="btn btn-link" type="button">
                                                            <tbody>
                                                                <td>{{data.id}}</td>
                                                                <td>{{data.speed}}</td>
                                                                <td>{{data.distance}}</td>
                                                                <td>{{data.updated_at}}</td>
                                                            </tbody>
                                                            <!-- Run ID:{{data.id}} Avg Speed:{{data.speed}} Avg Distance Skiied:{{data.distance}} Date Created:{{data.updated_at}} -->
                                                        </button>
                                                    </tr>
                                                <!-- </h2> -->
                                            <!-- </div> -->
                                        </div>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </html>
</template>

<script>
    export default {
        props:
        {
            name:'athlete-data',
            data:{
                type: Array,
            }
        },

        methods:{

        },

        data: function () {
            return{
                athletedata:{},   
            }
        },

        mounted(){
            this.athletedata = this.data    
        } 
}
</script>