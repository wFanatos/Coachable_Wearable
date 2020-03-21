<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Coachable</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>                       
                    @endauth
                </div>
            @endif

            <div class="container">
                <div class="title m-b-md content"> Coachable </div>
                    <p> This website was created by the Stardust Crusaders team for the purpose of presenting visualized data. </p>
                    <p> By clicking the login button in the top right corner, you are asked to provide your credentials. </p>
                    <p> Here is a list of different scenarios and their outcomes: </p>
                    <ul>
                        <li> User logs in with athlete credentials 
                            <ul> 
                                <li>User is brought to the /athlete screen where they are presented with information such as personal info, run info, org info, etc.</li>
                            </ul>
                        </li>
                        <li> User logs in with parent credentials 
                            <ul> 
                                <li>User is brought to the /parent screen where they are presented with information such as personal info and child info. </li>
                            </ul>
                        </li>
                        <li> User logs in with coach credentials 
                            <ul> 
                                <li>User is brought to the /coach screen where they are presented with information such as personal info and info about team members on current team (runs, events, etc.). </li>
                            </ul>
                        </li>
                        <li> User logs in with head coach credentials
                            <ul> 
                                <li>User is brought to the /head screen where they are presented with information such as personal info and all team info for teams in org. </li>
                            </ul> 
                        </li>
                    </ul>             
        </div>
    </body>
</html>
