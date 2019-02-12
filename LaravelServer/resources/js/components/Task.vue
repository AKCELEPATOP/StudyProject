<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3><span class="glyphicon glyphicon-dashboard"></span> Posts List </h3> <br>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-striped table-responsive" v-if="tasks.length > 0">
                            <tbody>
                            <tr>
                                <th>
                                    No.
                                </th>
                                <th>
                                    Method
                                </th>
                                <th>
                                    Url
                                </th>
                                <th>
                                    Body
                                </th>
                                <th>
                                    Date
                                </th>
                            </tr>
                            <tr v-for="(task, index) in tasks">
                                <td>{{ index + 1 }}</td>
                                <td>
                                    {{ task.method }}
                                </td>
                                <td>
                                    {{ task.route }}
                                </td>
                                <td>
                                    {{ task.body }}
                                </td>
                                <td>
                                    {{ task.created_at }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        name: "Task",
        data(){
            return {

            }
        },
        mounted()
        {
            this.readTasks();
        },
        methods: {

            readTasks()
            {
                axios.get('http://laravel.test/task')
                    .then(response => {
                        console.log(response.data);
                        this.tasks = response.data.tasks;
                    });
            },
        }
    }
</script>

<style scoped>

</style>
