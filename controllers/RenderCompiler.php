<?php
/**
 * RenderCompiler.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Parses the controls.xml file and initialises the Commands (1 Command per path) in the MVC framework with the correct renderer
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class RenderCompiler {
    /**
     *
     * @var \commands\DefaultCommand Handle to DefaultCommand class 
     */
    private static $defaultcmd = \commands\DefaultCommand::class;

    /**
     * Parse the controls.xml file
     * 
     * @param string $file Name of controls.xml file
     * @return Conf Object containing set of Commands
     */
    public function parseFile(string $file): Conf {
        $options = \simplexml_load_file($file);
        return $this->parse($options);
    }
    
    /**
     * Helper function to parse the file
     * 
     * @param \SimpleXMLElement $options
     * @return \controllers\Conf
     * @throws Exception
     */
    private function parse(\SimpleXMLElement $options): Conf {
        $conf = new Conf();
        foreach ($options->command as $command) {
            $path = (string)$command['path'];
            $cmdstr = (string)$command['class'];
            $path = (empty($path)) ? "/" : $path;
            $cmdstr = (empty($cmdstr)) ? self::$defaultcmd : $cmdstr;
            $pathobj = new RenderComponentDescriptor($path, $cmdstr);
            
            $this->processRenderComponentDescription($pathobj, 0, $command);
            
            if (isset($command->status) && isset($command->status['value'])) {
                foreach ($command->status as $statusel) {
                    $status = (string)$statusel['value'];
                    $statusval = constant(Command::class . "::" . $status);
                    
                    if(is_null($statusval)) {
                        throw new Exception("unknown status: {$status}");
                    }
                    
                    $this->processRenderComponentDescription($pathobj, $statusval, $statusel);
                }
            }
            
            $conf->set($path, $pathobj);
        }
        return $conf;
    }
    
    /**
     * Helper function to analyze a Command description in the controls file
     * 
     * @param \controllers\ComponentDescriptor $pathobj
     * @param int $statusval
     * @param \SimpleXMLElement $el
     */
    private function processRenderComponentDescription(RenderComponentDescriptor $pathobj, int $statusval, \SimpleXMLElement $el): void {
        if (isset($el->view) && isset($el->view['name'])) {
            $pathobj->setRenderer($statusval, new ViewRenderComponent((string)$el->view['name']));
        }
        
        if (isset($el->forward) && isset($el->forward['path'])) {
            $pathobj->setRenderer($statusval, new ForwardRenderComponent((string)$el->forward['path']));
        }
        
        if (isset($el->datarequest) && isset($el->datarequest['type'])) {
            $pathobj->setRenderer($statusval, DatarequestRenderComponent::init((string)$el->datarequest['type']));
        }
    }
}