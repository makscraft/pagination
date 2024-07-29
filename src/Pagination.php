<?php
namespace Rivacraft;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class for splitting the long lists of items into pages.
 * Creates params for SQL queries to use in LIMIT/OFFSET constructions.
 * Also generates array of links to use in html templates.
 * 
 * (c) Maxim Zaykov <mszaykov@gmail.com>
 * 
 * See examples at https://github.com/rivacraft/pagination
 */
class Pagination
{
    /**
     * Total number of items in list.
     * @var int
     */
    private int $total = 0;
    
    /**
     * Limit of items per one page.
     * @var int
     */
    private int $limit = 0;
    
    /**
     * Number of pages (intervals) according to total and limit.
     * @var int
     */
    private int $pages = 0;
    
    /**
     * Current page number.
     * @var int
     */
    private int $current = 0;

    /**
     * Target url to add pages GET params like '/catalog/new'
     * @var string
     */
    private string $path = '';

    /**
     * Creates object
     * @param int $total number of items (records, entities) 
     * @param int $limit items per 1 page
     */
    public function __construct(int $total = 0, int $limit = 0)
    {
        if($total > 0 && $limit > 0)
            $this -> run($total, $limit);
    }

    /**
     * Gets current page from GET if passed and runs pagination object.
     * Needs Symfory Request class.
     */
    public function run(int $total, int $limit): self
    {
        if($total <= 0 || $limit <= 0)
            return $this;

        $this -> total = $total;
        $this -> limit = $limit;
        $this -> pages = (int) ceil($this -> total / $this -> limit);

        $request = Request::createFromGlobals();

        $page = (int) $request -> query -> get('page');
        $this -> current = ($page > 0 && $page <= $this -> pages) ? $page : 1;

        return $this;
    }

    /**
     * Sets path (url) for adding pages to.
     */
    public function setPath(string $path): self
    {
        $this -> path = $path;

        return $this;
    }

    /**
     * Gets current url of pagination.
     */
    public function getPath(): string
    {
        return $this -> path;
    }

    /**
     * Returns limit of items per page.
     */
    public function getLimit(): int
    {
        return $this -> limit;
    }

    /**
     * Returns total number of items in paginator.
     */
    public function getTotal(): int
    {
        return $this -> total;
    }

    /**
     * Returns number of pages in paginator according to total and limit values.
     */
    public function getPages(): int
    {
        return $this -> pages;
    }

    /**
     * Returns number of current page.
     */
    public function getCurrent(): int
    {
        return $this -> current;
    }

    /**
     * Returns offset value for SQL query.
     */
    public function getOffset(): int
    {
        return ($this -> current - 1) * $this -> limit;
    }

    /**
     * Returns url param with current page number to use it in urls.
     */
    public function getUrlParams(): string
	{	
		return ($this -> current > 1) ? 'page='.$this -> current : '';
	}

    /**
     * Adds current page to passed url.
     */
    public function addUrlParams(string $path): string
	{
		if($this -> current <= 1)
			return $path;

		$path .= (strpos($path, '?') === false) ? '?' : '&';    
		
		return $path.$this -> getUrlParams();
	}

    /**
     * Checks if paginator has the number of pages greater than 1.
     */
    public function hasPages(): bool
    {
        return ($this -> pages > 1); 
    }

    /**
     * Returns set of values to display pagination links in html template.
     */
    public function getDisplayData(int $visible = 8): array
    {
        if(!$this -> hasPages())
            return [];

        $path = $this -> path.(strpos($this -> path, '?') === false ? '?page=' : '&page=');

        $data = [
            'prev_link' => null,
            'next_link' => null,
            'active' => $this -> current,
            'pages' => []
        ];

        if($this -> current > 1)
            $data['prev_link'] = $path.($this -> current - 1);

        if($this -> current < $this -> pages)
            $data['next_link'] = $path.($this -> current + 1);

        $intervals = [];
      
        $current_left = $this -> current - 1;
        $current_right = $this -> pages - $this -> current;
        $half = intval(ceil($visible / 2));
        
        if($current_left > $half && $current_right > $half)
        {
            $i = $current_left - $half + 2;
            
            while($i < $this -> current + $half && $i < $this -> pages)
                $intervals[] = $i ++;
        }
        else if($current_left > $half)
        {
            $i = $this -> pages;
            
            while($i > $this -> pages - $half - 3 && $i > 0)
                array_unshift($intervals, $i --);
        }
        else if($current_right > $half)
        {
            $i = 1;
            
            while($i < $visible - 1 && $i <= $this -> pages)
                $intervals[] = $i ++;
        }
        else
        {
            $i = 1;
            
            while($i <= $this -> pages)
                $intervals[] = $i ++;       
        }

        if($intervals[0] > 1)
        {
            $data['pages'][] = ['url' => $path, 'number' => 1];

            if($intervals[0] != 2)
                $data['pages'][] = ['url' => $path.($intervals[0] - 1), 'number' => '...'];
        }

        foreach($intervals as $value)
            $data['pages'][] = ['url' => $path.$value, 'number' => $value];

        if($intervals[count($intervals) - 1] < $this -> pages)
        {
            if($intervals[count($intervals) - 2] != $this -> pages -1)
                $data['pages'][] = ['url' => $path.($intervals[count($intervals) - 1] + 1), 'number' => '...'];

            $data['pages'][] = ['url' => $path.$this -> pages, 'number' => $this -> pages];
        }

        return $data;
    }
}