:loop
@echo RUNNING PM
@c:\php\php index.php
@REM timeout 1
@REM set /a loopcount=loopcount-1
@REM if %loopcount%==0 goto exitloop
@goto loop