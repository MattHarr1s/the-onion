<?php
require_once(dirname(dirname(__DIR__)). "php/classes/article.php");

/**
 * Class Article for the website "The Onion"
 *
 * This class can be used for any online publication.
 *
 * @author Matt Harris <mateodelay@gmail.com>
 **/
class Article{
	/**
	 * id for this article; this is the primary key
	 * @var int $articleId
	 **/
	private $articleId;
	/**
	 * actual textual content of this Article
	 * @var string $articleContent
	 **/
	private $articleContent;
	/**
	 * date and time this article was published, in a PHP DateTime object
	 * @var DateTime $articleDate
	 **/
	private $articleDate;
	/**
	 * issue this article belongs to
	 * @var string $issueId
	 **/
	private $issueId;

	/**
	 * constructor for this Article
	 *
	 * @param mixed $newArticleId id of this article or null if a new article
	 * @param string $articleContent string containing actual article data
	 * @param mixed $newArticleDate date and time Article was published or null if set to current date and time
	 * @param int $newIssueId id of the issue Article was published in
	 * @throws InvalidArgumentException if data types are not valid
	 * @throws RangeException if data values are out of bounds (e.g. strings too long, negative integers
	 * @throws Exception if some other exception is thrown
	 **/
	public function __construct($newArticleId,$newArticleContent,$newArticleDate = null,$newIssueId){
		try{
			$this->setArticleId($newArticleId);
			$this->setArticleContent($newArticleContent);
			$this->setArticleDate($newArticleDate);
			$this->setIssueId($newIssueId);
	}catch(invalidArgumentException $invalidArgument){
			// rethrow the exception to the caller
			throw(new InvalidArgumentException($invalidArgument ->getMessage(),0,$invalidArgument));
	}catch(RangeException $range){
			// rethrow the exception to the caller
			throw(new RangeException($range->getMessage(),0,$range));
	}catch(Exception $exception) {
			// rethrow generic exception
			throw(new Exception($exception->getMessage(),0,$exception));
		}
	}
	/**
	 * accessor method for articleId
	 *
	 * @return mixed value of articleId
	 **/
	public function getArticleId(){
			return($this->articleId);
	}
	/**
	 * mutator method for articleId
	 *
	 * @param mixed $newArticleId new value of articleId
	 * @throws InvalidArgumentException if $newArticleId is not an integer
	 * @throws RangeException if $newArticleId is not positive
	 **/

	public function setArticleId ($newArticlceId){
		// base case: if the articleId is null, this is a new article without a mySQL assigned id (yet)
		if ($newArticlceId === false){
				$this->articleId = null;
				return;
		}
		// verify the articleId is valid
		$newArticleId = filter_var($newArticleId, FILTER_VALIDATE_INT);
		if($newArticlceId === false){
			throw(new InvalidArgumentException("article id is not a valid integer"));
		}
		// verify the articleId is positive
		if($newArticlceId <= 0){
			throw(new RangeException("article id is not positive"));
		}
		//convert and store the articleId
		$this->articleId = intval ($newArticleId);
	}
	/**
	 * accessor method for articeContent
	 *
	 * return string value of tweet content
	 **/
	public function getArticleContent(){
			return($this->articleContent);
	}
	/**
}


