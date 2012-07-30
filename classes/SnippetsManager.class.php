<?php

class SnippetsManager {

	private static $_instance;

	public static function getReference() {

		if(!isset(self::$_instance))
			self::$_instance = new self();

		return self::$_instance;

	}

	public function countOfSnippetByUser ($userId, $conditions = false) {

		try {
			$db = PDOSQLite::getDBLink();
			$requestString = 'SELECT COUNT(*) AS count FROM `snippets` WHERE ( `id_user` = :id_user OR `private` = 0 )';

			if(!empty($conditions)) {
				$requestString .= ' AND `' . $conditions->field;

				if($conditions->field === 'tags') {

					$requestString .= '` LIKE :';
					$param = '%' . strtolower($conditions->value) . '%';

				} elseif($conditions->field === 'category') {

					$requestString .= '` = :';
					$param = strtolower($conditions->value);
				}
				$requestString .= $conditions->field;

				$request = $db->prepare($requestString);
				$request->bindValue(':' . $conditions->field, $param, PDO::PARAM_STR);

			} else {
				$request = $db->prepare($requestString);
			}

			$request->bindValue(':id_user', $userId, PDO::PARAM_INT);
			$request->execute();

			return $request->fetch(PDO::FETCH_OBJ);

		} catch (Exception $e) {
			return false;
		}
	}

	public function getSnippetsByUser ($userId, $pageNumber) {

		try {
			$db = PDOSQLite::getDBLink();
			$request = $db->prepare('SELECT rowid AS id, * FROM `snippets` WHERE ( `id_user` = :id_user OR `private` = 0 ) ORDER BY `last_update` DESC LIMIT :limit_down, :limit_up');
			$request->bindValue(':id_user', $userId, PDO::PARAM_INT);
			$request->bindValue(':limit_down', ($pageNumber - 1) * NUM_SNIPPET_PER_PAGE, PDO::PARAM_INT);
			$request->bindValue(':limit_up', NUM_SNIPPET_PER_PAGE, PDO::PARAM_INT);
			$request->execute();

			$allSnippetOfUser = array();

			while ($result = $request->fetch(PDO::FETCH_ASSOC)) {
				$allSnippetOfUser[] = new Snippet($result);
			}

			return $allSnippetOfUser;

		} catch (Exception $e) {
			return false;
		}

	}

	public function getSnippetById($id) {

		try {
			$db = PDOSQLite::getDBLink();
			$request = $db->prepare('SELECT rowid AS id, * FROM `snippets` WHERE rowid = :id');
			$request->bindParam(':id', $id, PDO::PARAM_INT, 1);
			$request->execute();
			$result = $request->fetch(PDO::FETCH_ASSOC);
			$snippet = new Snippet($result);
			return $snippet;
		} catch(Exception $e) {
			return false;
		}

	}

	public function getPublicSnippets($userId, $pageNumber) {

		try {
			$db = PDOSQLite::getDBLink();
			$request = $db->prepare('SELECT rowid AS id, * FROM `snippets` WHERE `id_user` = :id_user AND `private` = 0 ORDER BY `last_update` DESC LIMIT :limit_down , :limit_up');
			$request->bindParam(':id_user', $userId, PDO::PARAM_INT, 1);
			$request->bindParam(':limit_down', ($pageNumber - 1) * NUM_SNIPPET_PER_PAGE, PDO::PARAM_INT);
			$request->bindParam(':limit_up', NUM_SNIPPET_PER_PAGE, PDO::PARAM_INT);
			$request->execute();

			$publicSnippets = array();

			while($result = $request->fetch(PDO::FETCH_ASSOC)) {
				$publicSnippets[] = new Snippet($result);
			}

			return $publicSnippets;
		} catch(Exception $e) {
			return array();
		}

	}

	public function getSnippetsMatchedByName($idUser, $snippetName, $pageNumber) {

		try {
			$db = PDOSQLite::getDBLink();
			$request = $db->prepare('SELECT rowid AS id, * FROM `snippets` WHERE `id_user` = :id_user AND `name` = :name ORDER BY `last_update` DESC LIMIT :limit_down , :limit_up');
			$request->bindValue(':id_user', $idUser, PDO::PARAM_INT);
			$request->bindValue(':name', $snippetName, PDO::PARAM_STR);
			$request->bindValue(':limit_down', ($pageNumber - 1) * NUM_SNIPPET_PER_PAGE, PDO::PARAM_INT);
			$request->bindValue(':limit_up', NUM_SNIPPET_PER_PAGE, PDO::PARAM_INT);
			$request->execute();

			$snippetsMatchedByName = array();

			while($result = $request->fetch(PDO::FETCH_ASSOC)) {
				$snippetsMatchedByName[] = new Snippet($result);
			}

			return $snippetsMatchedByName;
		} catch(Exception $e) {
			return array();
		}

	}

	public function getYoungerSnippets($userId, $timestamp, $pageNumber) {

		try {
			$db = PDOSQLite::getDBLink();
			$request = $db->prepare('SELECT rowid as id, * FROM `snippets` WHERE `id_user` = :id_user AND `last_update` >= :timestamp  ORDER BY `last_update` LIMIT :limit_down , :limit_up');
			$request->bindParam(':id_user', $userId, PDO::PARAM_INT, 1);
			$request->bindParam(':timestamp', $timestamp, PDO::PARAM_INT, 32);
			$request->bindParam(':limit_down', ($pageNumber - 1) * NUM_SNIPPET_PER_PAGE, PDO::PARAM_INT, 32);
			$request->bindParam(':limit_up', NUM_SNIPPET_PER_PAGE, PDO::PARAM_INT, 32);
			$request->execute();

			$youngerSnippet = array();

			while($result = $request->fetch(PDO::FETCH_ASSOC)) {
				$youngerSnippet[] = new Snippet($result);
			}

			return $youngerSnippet;
		} catch(Exception $e) {
			return array();
		}

	}

	public function getSnippetsByCategory($userId, $categoryName, $pageNumber) {

		try {
			$db = PDOSQLite::getDBLink();
			$request = $db->prepare('SELECT rowid AS id, * FROM `snippets` WHERE ( `id_user` = :id_user OR `private` = 0 ) AND `category` = :category ORDER BY `last_update` DESC LIMIT :limit_down , :limit_up');
			$request->bindValue(':id_user', $userId, PDO::PARAM_INT);
			$request->bindValue(':category', strtolower($categoryName), PDO::PARAM_STR);
			$request->bindValue(':limit_down', ($pageNumber - 1) * NUM_SNIPPET_PER_PAGE, PDO::PARAM_STR);
			$request->bindValue(':limit_up', NUM_SNIPPET_PER_PAGE, PDO::PARAM_STR);
			$request->execute();

			$snippetsMatchedByCategory = array();

			while($result = $request->fetch(PDO::FETCH_ASSOC)) {
				$snippetsMatchedByCategory[] = new Snippet($result);
			}

			return $snippetsMatchedByCategory;
		} catch(Exception $e) {
			return array();
		}

	}

	public function getSnippetsByTag($userId, $tag, $pageNumber) {

		try {
			$db = PDOSQLite::getDBLink();
			$request = $db->prepare('SELECT rowid AS id, * FROM `snippets` WHERE ( `id_user` = :id_user OR `private` = 0 ) AND `tags` LIKE :tag ORDER BY `last_update` DESC LIMIT :limit_down , :limit_up');
			$request->bindValue(':id_user', $userId, PDO::PARAM_INT);
			$request->bindValue(':tag', '%' . strtolower($tag) . '%', PDO::PARAM_STR);
			$request->bindValue(':limit_down', ($pageNumber - 1) * NUM_SNIPPET_PER_PAGE, PDO::PARAM_STR);
			$request->bindValue(':limit_up', NUM_SNIPPET_PER_PAGE, PDO::PARAM_STR);
			$request->execute();

			$snippetsMatchedByTag = array();

			while($result = $request->fetch(PDO::FETCH_ASSOC)) {
				$snippetsMatchedByTag[] = new Snippet($result);
			}

			return $snippetsMatchedByTag;
		} catch(Exception $e) {
			return array();
		}

	}

	public function getAllCategories($userId) {

		try {
			$db = PDOSQLite::getDBLink();
			$request = $db->prepare('SELECT DISTINCT category FROM snippets WHERE id_user = :id_user');
			$request->bindParam(':id_user', $userId, PDO::PARAM_INT, 1);
			$request->execute();

			$categoryArray = array();

			while($result = $request->fetch(PDO::FETCH_ASSOC)) {
				$categoryArray[] = $result['category'];
			}

			return $categoryArray;
		} catch(Exception $e) {
			return array();
		}

	}
	
	public function getAllTags() {
		try 
		{
			$strTags = '';
			$tagsArray = array();
			
			$db = PDOSQLite::getDBLink();
			$request = $db->prepare('SELECT tags FROM snippets');
			$request->execute();

			//doing long string with all tags
			while($result = $request->fetch(PDO::FETCH_ASSOC)) 
			{
				$strTags .= ', '.$result['tags'];
			}
			//cut char ', '
			$strTags = substr($strTags, 2);
			//make new array of all individual tags
			$tagsArray = preg_split("/, ?/", $strTags);
			//make new array with all tags with the count of each in the $tagArray
			$arrtagsC = array_count_values($tagsArray);
			//sorting by keys reverse order
			arsort($arrtagsC);
			$i=0;
			$arrTagCloud= array();
			//Extract the 10 most used tags
			while (list($key, $value) = each($arrtagsC) and $i < 10)
			{
				$arrTagCloud[] = $key;
				$i++;
			}
			return array_unique($arrTagCloud);
		}
		catch(Exception $e) 
		{
			return array();
		}

	}

	public function instantSearch_countOfSnippets( $userId, $query, $category = false ) {

		try
		{
			$db = PDOSQLite::getDBLink();
			$requestString = 'SELECT COUNT(*) AS count FROM `snippets` WHERE ( `id_user` = :id_user OR `private` = 0 )';

			if ( !empty( $category ) )
				$requestString .= ' AND `category` = :category';

			$requestString .= ' AND `name` LIKE :keyword';

			$request = $db->prepare( $requestString );
			$request->bindValue( ':id_user', $userId, PDO::PARAM_INT );
			$request->bindValue( ':keyword', '%' . $query . '%', PDO::PARAM_STR );
			if ( !empty( $category ) )
				$request->bindValue( ':category', $category, PDO::PARAM_STR );

			$request->execute();

			return $request->fetch( PDO::FETCH_OBJ );
		}
		catch ( Exception $e )
		{
			return false;
		}
	}

	public function instantSearch_GetSnippetsByCategory($userId, $keyWord, $category, $pageNumber) {

		try {
			$db = PDOSQLite::getDBLink();
			$request = $db->prepare('SELECT `rowid` AS `id`, * FROM snippets WHERE ( `id_user` = :id_user OR `private` = 0 ) AND `category` = :category AND `name` LIKE :keyword ORDER BY `id_user` ASC, `last_update` DESC LIMIT :limit_down, :limit_up');
			$request->bindValue(':id_user', $userId, PDO::PARAM_INT);
			$request->bindValue(':category', $category, PDO::PARAM_STR);
			$request->bindValue(':keyword', '%' . $keyWord . '%', PDO::PARAM_STR);
			$request->bindValue(':limit_down', ($pageNumber - 1) * NUM_SNIPPET_PER_PAGE, PDO::PARAM_INT);
			$request->bindValue(':limit_up', NUM_SNIPPET_PER_PAGE, PDO::PARAM_INT);
			$request->execute();

			$arrayOfSnippetsByCategory = array();

			while($result = $request->fetch(PDO::FETCH_ASSOC)) {
				$arrayOfSnippetsByCategory[] = new Snippet($result);
			}

			return $arrayOfSnippetsByCategory;
		} catch(Exception $e) {
			return array();
		}

	}

	public function instantSearch_GetSnippets($userId, $keyWord, $pageNumber) {

		try {
			$db = PDOSQLite::getDBLink();
			$request = $db->prepare('SELECT `rowid` AS `id`, * FROM `snippets` WHERE ( `id_user` = :id_user OR `private` = 0 ) AND `name` LIKE :key_word ORDER BY `id_user` ASC, `last_update` DESC LIMIT :limit_down, :limit_up');
			$request->bindValue(':id_user', $userId, PDO::PARAM_INT);
			$request->bindValue(':key_word', '%' . $keyWord . '%', PDO::PARAM_STR);
			$request->bindValue(':limit_down', ($pageNumber -1) * NUM_SNIPPET_PER_PAGE, PDO::PARAM_INT);
			$request->bindValue(':limit_up', NUM_SNIPPET_PER_PAGE, PDO::PARAM_INT);
			$request->execute();

			$arrayOfSnippets = array();

			while($result = $request->fetch(PDO::FETCH_ASSOC)) {
				$arrayOfSnippets[] = new Snippet($result);
			}

			return $arrayOfSnippets;
		} catch(Exception $e) {
			return array();
		}

	}

	public function updateSnippetInfos($oldSnippetId, $newSnippet) {

		try {
			$db = PDOSQLite::getDBLink();
			$request = $db->prepare('UPDATE snippets SET name = :name, id_user = :id_user, last_update = :last_update, content = :content, language = :language, comment = :comment, category = :category, tags = :tags, private = :private WHERE rowid = :id');

			$request->bindValue(':id', $oldSnippetId, PDO::PARAM_INT);
			$request->bindValue(':name', $newSnippet->_name, PDO::PARAM_STR);
			$request->bindValue(':id_user', $newSnippet->_idUser, PDO::PARAM_INT);
			$request->bindValue(':last_update', $newSnippet->_lastUpdate, PDO::PARAM_INT);
			$request->bindValue(':content', $newSnippet->_content, PDO::PARAM_STR);
			$request->bindValue(':language', $newSnippet->_language, PDO::PARAM_INT);
			$request->bindValue(':comment', $newSnippet->_comment, PDO::PARAM_STR);
			$request->bindValue(':category', $newSnippet->_category, PDO::PARAM_STR);
			$request->bindValue(':tags', $newSnippet->_tags, PDO::PARAM_STR);
			$request->bindValue(':private', $newSnippet->_private, PDO::PARAM_INT);

			return $request->execute();
		} catch(Exception $e) {
			return false;
		}

	}

	public function deleteSnippetFromDB($idSnippet) {

		if(empty($idSnippet))
			return false;

		try {
			$db = PDOSQLite::getDBLink();
			$request = $db->prepare('DELETE FROM snippets WHERE rowid = :id');
			$request->bindParam(':id', $idSnippet, PDO::PARAM_INT, 1);
			return $request->execute();
		} catch(Exception $e) {
			return false;
		}

	}

	public function deleteSnippetsOfUser( $idUser ) {

		if ( empty( $idUser ) )
			return false;

		try {

			$db = PDOSQLite::getDBLink();
			$request = $db->prepare( 'DELETE FROM snippets WHERE id_user = :id' );
			$request->bindParam( ':id', $idUser, PDO::PARAM_INT);

			return $request->execute();

		} catch( Exception $e ) {

			return false;
		}
	}

	private function __construct() {}

}
