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
	 * @var int $issueId
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

	public function setArticleId ($newArticleId){
		// base case: if the articleId is null, this is a new article without a mySQL assigned id (yet)
		if ($newArticleId === false){
				$this->articleId = null;
				return;
		}
		// verify the articleId is valid
		$newArticleId = filter_var($newArticleId, FILTER_VALIDATE_INT);
		if($newArticleId === false){
			throw(new InvalidArgumentException("article id is not a valid integer"));
		}
		// verify the articleId is positive
		if($newArticleId <= 0){
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
	 * mutator method for article content
	 *
	 * @param string $newArticleContent new value of article content
	 * @throws InvalidArgumentException if $newArticleContent is not a string or insecure
	 **/
	public function setArticleContent($newArticleContent){
		// verify the article content is secure
		$newArticleContent = trim($newArticleContent);
		$newArticleContent = filter_var($newArticleContent, FILTER_SANITIZE_STRING);
		if(empty($newArticleContent) === true){
				throw(new InvalidArgumentException("article content is empty or insecure"));
		}
		//store the article content
		$this->articleContent = $newArticleContent;
	}
	/**
	 * accesssor method for article date
	 *
	 * @return DATETIME value of article date
	 **/
	public function getArticleDate(){
		return($this->articleDate);
	}
	/** mutator method for article date
	 *
	 * @param mixed $newArticleDate article date as a DateTime object or string (or null to load the currrent time)
	 * @throws InvalidArgumentException if $newArticleDate is not a valid object or string
	 * @throws RangeException if $newArticleDate is a date that does not exist
	 **/
	public function setArticleDate($newArticleDate){
		// base case:: if the date is null, use the current date and time
		if($newArticleDate === null){
				$this->articleDate = new DateTime();
				return;
		}
		// store the article date
		try{
			$newArticleDate = validateDate($newArticleDate);
		}catch(InvalidArgumentException $invalidArgument) {
			throw(new InvalidArgumentException($invalidArgument->getMessage(), 0, $invalidArgument));
		}catch (RangeException $range) {
			throw(new RangeException($range->getMessage(), 0, $range));
		}catch(Exception $exception){
			throw(new Exception($exception->getMessage(),0,$exception));
		}
		$this->articleDate = $newArticleDate;
	}
	/**
	 * accessor method for issueId
	 *
	 * @return int value of issueId
	 **/
	public function setIssueId($newIssueId){
		return($this->issueId);
	}
	/**
	 * mutator method for issueId
	 *
	 * @param int $newIssueId new value of issueId
	 * @throws InvalidArgumentException if $newIssueId is not an integer or not positive
	 * @throws RangeExeption if $newIssueId is not positive
	 **/
	public function setIssueId($newIssueId){
		// verify the issueId is valid
		$newIssueId = filter_var($newIssueId, FILTER_VALIDATE_INT);
		if($newIssueId === false){
			throw(new InvalidArgumentException("issue id is not a positive integer"));
		}
		// verify the newIssueId is positive
		if($newIssueId <= 0){
				throw(new RangeException("issue id is not positive"));
		}
		// convert and store the issue id
		$this->issueId = intval($newIssueId);
			}
/**
 * inserts this article into mySQL
 *
 * @param PDO $pdo PDO connection object
 * @throws PDOException when mySQL related errors occur
 **/
	public function insert(PDO&$PDO){
		//enforce the articleId is null( i.e., don't insert a tweet that already exists)
		if($this->articleId !== null){
				throw(new PDOException("not a new article"));
		}
		// create query template
		$query
	="INSERT INTO article(articleDate, issueId, articleContent) VALUES (:articleDate, :issueId,:articleContent)";
		$statement = $pdo->prepare($query);

		// bind the member values to the placeholders in the template
		$formattedDate= $this->articleDate->format("Y-m-d H:i:s");
		$parameters = array("articleDate"=>$formattedDate, $this"issueId"=>$this->issueId,"articleContent"=>$this->
		articleContent,);
		$statement->execute($parameters);

		// update the null articleId with what mySQL just gave us
		$this->articleId = intval($pdo->lastInsertId());
	}
/**
 * deletes this Article from mySQL
 *
 * @param PDO $pdo PDO connection object
 * @throws PDOException when mySQL related errors occur
 **/
	public function delete(PDO $pdo){
		// enforce the articleId is not null( i.e.,don't delete an article that hasn't been inserted)
		if($this->articleId === null){
			throw(new PDOException("unable to delete an article that does not exist"));
		}

		//create query template
		$query = "DELETE FROM article WHERE articleId = :articleId";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holder in the template
		$parameters = array("articleId" =>$this->articleId);
		$statement->execute($parameters);
	}
/**
 * updated this article in mySql
 *
 * @param PDO $pdo PDO connection object
 * @throws PDO exception when mySQL related errors occur
 **/
	public function update(PDO $pdo){
		// enforce the articleId is not null (i.e., don't update an article that hasn't been inserted
		if($this->articleId === null){
			throw (new PDOException("unable to update an article that does not exist"));
		}

		//create query template
		$query= "UPDATE article SET articleContent = :articleContent, articleDate = :articleDate, issueId = :issueID
	WHERE articleId = :articleId";
		$statement = $pdo->prepare($query);

		//bind the member variables to the place holders in the template
		$formattedDate = $this->articleDate->format("Y-m-d H:i:s");
		$parameters = array("articleContent"=>$this->articleContent,"articleDate"=>$this->$formattedDate,"articleId"=>
	$this->articleId);
		$statement->execute($parameters);
	}
}




