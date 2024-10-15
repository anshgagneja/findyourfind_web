<style type="text/css">
  body{
    background-color: #08AEEA;
    background-image: linear-gradient(0deg, #08AEEA 0%, #2AF598 100%);
    height: 800px;
  }
        .water{
            width:200px;
            height: 200px;
            background-color: #ffc107;
            border-radius: 50%;
            position: relative;
            box-shadow: inset 0 0 30px 0 rgba(0,0,0,.5), 0 4px 10px 0 rgba(0,0,0,.5);
            overflow: hidden;
        }
        .water:before, .water:after{
            content:'';
            position: absolute;
            width:400px;
            height: 400px;
            top:-150px;
            background-color: #fff;
        }
        .water:before{
            border-radius: 45%;
            background:rgba(255,255,255,.7);
            animation:wave 5s linear infinite;
        }
        .water:after{
            border-radius: 35%;
            background:rgba(255,255,255,.3);
            animation:wave 5s linear infinite;
        }
        @keyframes wave{
            0%{
                transform: rotate(0);
            }
            100%{
                transform: rotate(360deg);
            }
        }
</style>
  <body>
    <!-- Admin Panel HTML codes will be written here(Starts)-->

    <div class="container-fluid">
      <div class="row" style="padding-top: 10%">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="text-align: center;
            background-color: #ffffff33; border-radius: 20px;
            padding:30px;">

                <div class="water" style="margin-left: 100px; margin-bottom: 20px "></div>
                <span style="font-weight: 100;color: black">Authenticating Your Account...</span>
            </div>
            <div class="col-md-4"></div>
      </div>
    </div>

    <!-- Admin Panel HTML codes will be written here (End)-->
    <meta http-equiv="refresh" content="2;url=index.php?page=Dashboard" />