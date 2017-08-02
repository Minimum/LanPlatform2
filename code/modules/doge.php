<?php
    function dogeInit($manager)
    {
        dogeTSInit($manager);
        
        return;
    }
    
/*
    $article = new DogeArticle;
    $article->id = $manager->registerArticle();
    $article->name = "car_spawn";
    $article->content = "";
    $section->addArticle($article);
*/

    class DogeManager
    {
        protected $sections = array();
        protected $totalSections = 0;
        protected $totalArticles = 0;
    
        public function addSection($section)
        {
            $section->id = $this->totalSections;
        
            $this->sections[$this->totalSections] = $section;
            
            $this->totalSections++;
            
            return;
        }
        
        public function registerArticle()
        {
            $id = $this->totalArticles;
            
            $this->totalArticles++;
            
            return $id;
        }
        
        public function printInfo()
        {
            // Start Doge Section
            $output = "\n\"doge\": {";
            
            // Start Section Array
            $output .= "\n\"sections\": [";
            
            // Write Sections
            for($x = 0; $x < $this->totalSections; $x++)
            {
                if($x != 0)
                {
                    $output .= ",";
                }
                
                $output .= "\n".$this->sections[$x]->toString();
            }
            
            // End Section Array
            $output .= "\n]";
            
            // End Doge Section
            $output .= "\n}";
            
            return $output;
        }
    }
    
    class DogeSection
    {
        public $id = 0;
        public $name = "";
        public $articles = array();
        public $totalArticles = 0;
        
        public function addArticle($article)
        {
            $this->articles[$this->totalArticles] = $article;
            
            $this->totalArticles++;
            
            return;
        }
        
        public function toString()
        {
            // Start Object
            $output = "{\n";
            
            // Id
            $output .= "\"id\": \"".$this->id."\",\n";
            
            // Name
            $output .= "\"name\": \"".$this->name."\",\n";
            
            // Start Articles
            $output .= "\"articles\":\n[";
            
            $numArticles = 0;
            
            foreach($this->articles as $article)
            {
                if($numArticles != 0)
                {
                    $output .= ",";
                }
                
                $output .= "\n".$article->toString();
                
                $numArticles++;
            }
            
            // End Articles
            $output .= "\n]";
            
            // End Object
            $output .= "\n}";
            
            return $output;
        }
    }
    
    class DogeArticle
    {
        public $id = 0;
        public $name = "";
        public $content = "";
        
        public function toString()
        {
            // Start Object
            $output = "{\n";
            
            // Id
            $output .= "\"id\": \"".$this->id."\",\n";
            
            // Name
            $output .= "\"name\": \"".$this->name."\",\n";
            
            // Content
            $output .= "\"content\": ".json_encode($this->content)."\n";
            
            // End Object
            $output .= "}";
            
            return $output;
        }
    }
    
    function dogeTSInit($manager)
    {
        $section = new DogeSection;
        
        $section->name = "The Specialists 2.1";
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Spawn Car";
        $article->content = "<span class=\"contentTitle\">car_spawn</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            <i>None</i>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Spawns the killer car.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Set Car Mode";
        $article->content = "<span class=\"contentTitle\">car_set &#60;mode&#62;</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            Mode - [Required]<br />
                            <div style=\"margin-left:8px\">
                                0: Disabled<br />
                                1: Chase<br />
                                2: Player Controlled (Unfinished)<br />
                                3: Weeping
                            </div>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Sets the killer car's mode.<br />
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Get Car ID";
        $article->content = "<span class=\"contentTitle\">car_getid</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            <i>None</i>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Returns the internal pointer entity id of the killer car.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Set Car Laser";
        $article->content = "<span class=\"contentTitle\">car_setlaser</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            <i>None</i>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Toggles the car's laser weaponry.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Test Car Laser";
        $article->content = "<span class=\"contentTitle\">car_testlaser &#60;mode&#62;</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            Mode -<br />
                            <div style=\"margin-left:8px\">
                                default: Shoots at the car's target OR you.<br />
                                1: Shoots at your target location.
                            </div>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Test fires the laser.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Rob";
        $article->content = "<span class=\"contentTitle\">say /rob</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            <i>None</i>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Robs the store on the map (if supported.)
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Rob Toggle";
        $article->content = "<span class=\"contentTitle\">say /robtoggle</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            <i>None</i>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Toggles the auto rob script for the store on the map (if supported.)
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Infinite Ammo";
        $article->content = "<span class=\"contentTitle\">say /infiniteammo</span><br />
                        <span class=\"contentTitle\">say /infammo</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            <i>None</i>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Toggles infinite ammo for everyone.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Infinite Reloads";
        $article->content = "<span class=\"contentTitle\">say /infinitereloads</span><br />
                        <span class=\"contentTitle\">say /infreloads</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            <i>None</i>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Toggles infinite reloads for everyone.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Infinite Slots";
        $article->content = "<span class=\"contentTitle\">say /infiniteslots</span><br />
                        <span class=\"contentTitle\">say /infslots</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            <i>None</i>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Toggles infinite weapon slots for everyone.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Regen Health";
        $article->content = "<span class=\"contentTitle\">say /regenhealth</span><br />
                        <span class=\"contentTitle\">say /regen</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            <i>None</i>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Toggles health regeneration for everyone.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Perma Fire Rate";
        $article->content = "<span class=\"contentTitle\">say /togglerof</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            Rate - [Required]<br />
                            <div style=\"margin-left:8px\">
                                -1: Disabled<br />
                                0: Full Auto<br />
                                1: Semi Auto<br />
                                2: Burst<br />
                                3: Pump Action<br />
                                4: Free Semi<br />
                                5: Free Full
                            </div>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Permanently sets your rate of fire.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Temp Fire Rate";
        $article->content = "<span class=\"contentTitle\">say /setrof</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            Rate - [Required]<br />
                            <div style=\"margin-left:8px\">
                                0: Full Auto<br />
                                1: Semi Auto<br />
                                2: Burst<br />
                                3: Pump Action<br />
                                4: Free Semi<br />
                                5: Free Full
                            </div>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Sets your weapon's rate of fire.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Perma Weapon Flags";
        $article->content = "<span class=\"contentTitle\">say /toggleflags</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            Flags - [Required]<br />
                            <div style=\"margin-left:8px\">
                                -1: Disabled<br />
                                0: None<br />
                                1: Silenced<br />
                                2: Lasersight<br />
                                4: Flashlight<br />
                                8: Scope
                            </div>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Permanently sets your weapon attachment flags.<br />
                            Add flag ids together to get your desired combo.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Temp Weapon Flags";
        $article->content = "<span class=\"contentTitle\">say /setflags</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            Flags - [Required]<br />
                            <div style=\"margin-left:8px\">
                                0: None<br />
                                1: Silenced<br />
                                2: Lasersight<br />
                                4: Flashlight<br />
                                8: Scope
                            </div>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Sets your weapon's attachment flags.<br />
                            Add flag ids together to get your desired combo.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Set Slots";
        $article->content = "<span class=\"contentTitle\">say /setslots</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            Slots - [Required]<br />
                            <div style=\"margin-left:8px\">
                                Number of slots to set.
                            </div>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Sets your weapon slots.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Set Time";
        $article->content = "<span class=\"contentTitle\">say /settime</span><br />
                        <span class=\"contentTitle\">say /toggletime</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            Timescale - [Required]<br />
                            <div style=\"margin-left:8px\">
                                How fast time will go.<br />
                                Example: 1.0 is normal.  2.0 is double speed.  0.5 is half speed.
                            </div>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Sets your time speed.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Enable Flying";
        $article->content = "<span class=\"contentTitle\">fly_enable</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            <i>None</i>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Toggles air acceleration.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Enable Instagib";
        $article->content = "<span class=\"contentTitle\">instagib</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            <i>None</i>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Check it out!
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Add Weapon Spawn";
        $article->content = "<span class=\"contentTitle\">ts_spawnweapon</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            weaponid - [Required]<br />
                            <div style=\"margin-left: 8px;\">
                                allan please add weaponids here
                            </div>
                            clip -<br />
                            <div style=\"margin-left:8px\">
                                Amount of ammo in the magazine.
                            </div>
                            flags -
                            <div style=\"margin-left:8px\">
                                0: None<br />
                                1: Silenced<br />
                                2: Lasersight<br />
                                4: Flashlight<br />
                                8: Scope<br />
                                16: On Wall
                            </div>
                            duration -
                            <div style=\"margin-left:8px\">
                                Time in seconds between respawn.
                            </div>
                            alpha -
                            <div style=\"margin-left:8px\">
                                This doesn't work yet.
                            </div>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Creates a weapon spawn.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Add Saved Weapon Spawn";
        $article->content = "<span class=\"contentTitle\">ts_addweaponspawn</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            weaponid - [Required]<br />
                            <div style=\"margin-left: 8px;\">
                                allan please add weaponids here
                            </div>
                            clip -<br />
                            <div style=\"margin-left:8px\">
                                Amount of ammo in the magazine.
                            </div>
                            flags -
                            <div style=\"margin-left:8px\">
                                0: None<br />
                                1: Silenced<br />
                                2: Lasersight<br />
                                4: Flashlight<br />
                                8: Scope<br />
                                16: On Wall
                            </div>
                            duration -
                            <div style=\"margin-left:8px\">
                                Time in seconds between respawn.
                            </div>
                            alpha -
                            <div style=\"margin-left:8px\">
                                This doesn't work yet.
                            </div>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Creates a weapon spawn and saves it to the database.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Set Personal Gravity";
        $article->content = "<span class=\"contentTitle\">say /setgravity</span><br />
                        <span class=\"contentTitle\">say /setgrav</span><br />
                        <span id=\"accountTitle2\">Arguments:</span><br />
                        <div id=\"accountText\">
                            gravity - [Required]<br />
                            <div style=\"margin-left: 8px;\">
                                The multiplier of gravity relative to the world's gravity.
                            </div>
                        </div>
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Sets your gravity relative to the world's gravity.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Sounds";
        $article->content = "<span class=\"contentTitle\">say /universal</span><br />
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Plays the Universal theme.
                        </div><br />
                        <span class=\"contentTitle\">say /merica</span><br />
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Plays the Merica theme.
                        </div><br />
                        <span class=\"contentTitle\">say /bird</span><br />
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Plays the Bird theme.
                        </div><br />
                        <span class=\"contentTitle\">say /billnye</span><br />
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Plays the Bill Nye theme.
                        </div><br />
                        <span class=\"contentTitle\">say /doakes</span><br />
                        <span id=\"accountTitle2\">Description:</span><br />
                        <div id=\"accountText\">
                            Plays the Doakes theme.
                        </div><br />";
        $section->addArticle($article);
        
        $article = new DogeArticle;
        $article->id = $manager->registerArticle();
        $article->name = "Cvars";
        $article->content = "<div class=\"contentTitle\">Cvars</div>

                        <span id=\"accountTitle\">carmovement</span><br />
                        <span id=\"accountTitle2\">[Float] -Infinity to Infinity</span><br />
                        <div id=\"accountText\">
                            The speed of the killer car.
                        </div><br />

                        <span id=\"accountTitle\">carrate</span><br />
                        <span id=\"accountTitle2\">[Float] 0.0 to Infinity</span><br />
                        <div id=\"accountText\">
                            The killer car's \"thinking\" rate per second.
                        </div><br />

                        <span id=\"accountTitle\">carlaserrate</span><br />
                        <span id=\"accountTitle2\">[Float] 0.0 to Infinity</span><br />
                        <div id=\"accountText\">
                            Time in seconds until the killer car's laser fires at a player looking at it.
                        </div><br />

                        <span id=\"accountTitle\">carhuddebug</span><br />
                        <span id=\"accountTitle2\">[Bool] 0 to 1</span><br />
                        <div id=\"accountText\">
                            Enables debug hud text for the killer car.
                        </div><br />

                        <span id=\"accountTitle\">carsndchase</span><br />
                        <span id=\"accountTitle2\">[Bool] 0 to 1</span><br />
                        <div id=\"accountText\">
                            Enables the killer car's chase sound effects.
                        </div><br />

                        <span id=\"accountTitle\">carsndsurprise</span><br />
                        <span id=\"accountTitle2\">[Bool] 0 to 1</span><br />
                        <div id=\"accountText\">
                            Enables the killer car's surprise sound effect.
                        </div><br />

                        <span id=\"accountTitle\">lan_robrate</span><br />
                        <span id=\"accountTitle2\">[Float] 0.0 to Infinity</span><br />
                        <div id=\"accountText\">
                            Times per second that the auto rob script is run.
                        </div><br />

                        <span id=\"accountTitle\">lan_regendelay</span><br />
                        <span id=\"accountTitle2\">[Float] 0.0 to Infinity</span><br />
                        <div id=\"accountText\">
                            Time in seconds that health regeneration is delayed when hurt.
                        </div><br />

                        <span id=\"accountTitle\">lan_regentime</span><br />
                        <span id=\"accountTitle2\">[Float] 0.0 to Infinity</span><br />
                        <div id=\"accountText\">
                            Length of intervals in seconds that health is regenerated.
                        </div><br />

                        <span id=\"accountTitle\">lan_regenamount</span><br />
                        <span id=\"accountTitle2\">[Float] 0.0 to Infinity</span><br />
                        <div id=\"accountText\">
                            Amount of health regenerated per interval.
                        </div><br />

                        <span id=\"accountTitle\">lan_regenmax</span><br />
                        <span id=\"accountTitle2\">[Float] 0.0 to Infinity</span><br />
                        <div id=\"accountText\">
                            Max amount of health to regenerate to.
                        </div><br />

                        <span id=\"accountTitle\">lan_ammorate</span><br />
                        <span id=\"accountTitle2\">[Float] 0.0 to Infinity</span><br />
                        <div id=\"accountText\">
                            Time in seconds between ammo replenishes.
                        </div><br />

                        <span id=\"accountTitle\">lan_tsslots</span><br />
                        <span id=\"accountTitle2\">[Int] 0 to Infinity</span><br />
                        <div id=\"accountText\">
                            Max amount of weapon slots.
                        </div><br />

                        <span id=\"accountTitle\">lan_tsspeed</span><br />
                        <span id=\"accountTitle2\">[Float] 0.0 to Infinity</span><br />
                        <div id=\"accountText\">
                            Player speed.
                        </div><br />

                        <span id=\"accountTitle\">fly_maxspeed</span><br />
                        <span id=\"accountTitle2\">[Float] 0.0 to Infinity</span><br />
                        <div id=\"accountText\">
                            Max flight speed.
                        </div><br />

                        <span id=\"accountTitle\">fly_acceleration</span><br />
                        <span id=\"accountTitle2\">[Float] -Infinity to Infinity</span><br />
                        <div id=\"accountText\">
                            Flight forward acceleration.
                        </div><br />

                        <span id=\"accountTitle\">fly_lift</span><br />
                        <span id=\"accountTitle2\">[Float] -Infinity to Infinity</span><br />
                        <div id=\"accountText\">
                            Flight upwards lift.
                        </div><br />

                        <span id=\"accountTitle\">sv_instagib</span><br />
                        <span id=\"accountTitle2\">[Bool] 0 to 1</span><br />
                        <div id=\"accountText\">
                            Enables instagib mode.
                        </div><br />

                        <span id=\"accountTitle\">sv_instaspawn</span><br />
                        <span id=\"accountTitle2\">[Bool] 0 to 1</span><br />
                        <div id=\"accountText\">
                            Enables instaspawn mode.
                        </div>";
        $section->addArticle($article);
        
        $manager->addSection($section);
        
        return;
    }
?>