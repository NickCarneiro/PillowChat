<?php
class chatDocument extends phpillowDocument
{
    protected static $type = 'chat_message';
	protected $versioned = false;
    protected $requiredProperties = array(
        'message',
        'timestamp',
		'username'
    );

    public function __construct()
    {
        $this->properties = array(
            'message'     => new phpillowStringValidator(),
            'username'      => new phpillowStringValidator(),
			'timestamp'      => new phpillowStringValidator(),
			'tripcode'		=> new phpillowStringValidator()
            
        );

        parent::__construct();
    }

    protected function generateId()
    {
        return null;
    }

    protected function getType()
    {
        return self::$type;
    }
}
?>