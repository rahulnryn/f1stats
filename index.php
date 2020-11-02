<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <script src="jquery.min.js"></script>
    <script language="JavaScript">
      $(document).ready(function(){
          $('#years').on('change', function(){
              var yearsID = $(this).val();
              $.ajax({
                  type:'POST',
                  url:'teams.php',
                  data:'yearsID='+yearsID,
                  success:function(html){
                      $('#teams').html(html);
                  }
              }); 
          });
      });
    </script>
    
    <style type = "text/css">
       
        .buttons{
          height: 30px;
          width: 250px;
        }
        .submitbox{
          height: 400px;
          width:300px;
          margin: 0 auto;
          padding: 0.6em;
          display:block;
        }
        h2{
          color: #436EEE;
          font-family: "Sans-Serif, Peace-Sans";
        }
        body{
          background: #f0e79d;
        }
        .year{
          font-size:20px;
          font-family: "Gotham-Black";
        }
        .car{
          font-size:20px;
          font-family: "Gotham-Black";
        }
        h5{
          color: green;
        }
        .subbut{
          height: 30px;
          width: 150px;
        }
       
    </style>
    <title>F1 Teammate Comparisons</title>
  </head>
  <body>
        <br><br><br><br><br><br><br>
        <div class = "submitbox">
        <h2> Choose teammates/team for driver comparison </h2>

          <label class="year">Choose a Year:</label>
          <br> <br>
          <form action="f1analysis.php"  method="post">
            <select id = "years" name="years" class = "buttons">
              <option value="">Select</option>
              <option value="1980">1980</option>
              <option value="1981">1981</option>
              <option value="1982">1982</option>
              <option value="1983">1983</option>
              <option value="1984">1984</option>
              <option value="1985">1985</option>
              <option value="1986">1986</option>
              <option value="1987">1987</option>
              <option value="1988">1988</option>
              <option value="1989">1989</option>
              <option value="1990">1990</option>
              <option value="1991">1991</option>
              <option value="1992">1992</option>
              <option value="1993">1993</option>
              <option value="1994">1994</option>
              <option value="1995">1995</option>
              <option value="1996">1996</option>
              <option value="1997">1997</option>
              <option value="1998">1998</option>
              <option value="1999">1999</option>
              <option value="2000">2000</option>
              <option value="2001">2001</option>

              <option value="2002">2002</option>
              <option value="2003">2003</option>
              <option value="2004">2004</option>
              <option value="2005">2005</option>
              <option value="2006">2006</option>
              <option value="2007">2007</option>
              <option value="2008">2008</option>
              <option value="2009">2009</option>
              <option value="2010">2010</option>
              <option value="2011">2011</option>
              <option value="2012">2012</option>
              <option value="2013">2013</option>
              <option value="2014">2014</option>
              <option value="2015">2015</option>
              <option value="2016">2016</option>
              <option value="2017">2017</option>
              <option value="2018">2018</option>
              <option value="2019">2019</option>
              <option value="2020">2020</option>
            </select>
          <br><br>
          <label class="car">Choose a Team:</label>
          <br> <br>
          <select id="teams" name="teams" class ="buttons">
            <option value=""> Select </option>
          </select>
          <br> <br>
          <input type="submit" name="submit" value="Submit" class="subbut" />

        </form>
        <h5> Built using ERGAST database (Results will be slightly delayed)  </h5>

      </div>
  </body>

</html>

