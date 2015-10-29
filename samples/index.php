<html>
	<head>
		<title>Nemiro.Data.PHP</title>
	</head>
	<body>
		<h1>Nemiro.Data.PHP</h1>
		<p><strong>Nemiro.Data.PHP</strong> is a small set of utility classes for working with databases <strong>MySql</strong> and <strong>PostgreSQL</strong>.</p>
		<p>To work with the databases used five simple methods: <strong>ExecuteNonQuery</strong>, <strong>ExecuteScalar</strong>, <strong>GetData</strong>, <strong>GetTable</strong> and <strong>GetRow</strong>.</p>
		<p><a href="https://github.com/alekseynemiro/Nemiro.Data.PHP">https://github.com/alekseynemiro/Nemiro.Data.PHP</a></p>
		<hr />
		<p>
			To test the samples you need to configure the database connections in the <strong>config.php.</strong>
			Also necessary to created test tables: [users], [messages] and [stat] or you can just change the test queries.
		</p>
		<p>Using the <strong>MySql</strong> and <strong>PgSql</strong> classes is no different. The difference is only in <strong>SQL</strong> queries.</p>
		<h2>MySql</h2>
		<ul>
			<li>
				<a href="mysql_simply.php">Simply</a><br />
				<small>(DBCommand without and with parameters; GetTable, ExecuteNonQuery methods)</small>
			</li>
			<li>
				<a href="mysql_insert_get_id.php">Insert and get ID</a><br />
				<small>(DBCommand with parameters, ExecuteScalar method)</small>
			</li>
			<li>
				<a href="mysql_getrow.php">Get single row</a><br />
				<small>(DBCommand with parameters, GetRow method)</small>
			</li>
			<li>
				<a href="mysql_gettable.php">Get array of rows (table)</a><br />
				<small>(DBCommand with parameters, GetTable method)</small>
			</li>
			<li>
				<a href="mysql_getdata.php">Get array of tables (DataSet)</a><br />
				<small>(multiple DBCommand with parameters, GetData method)</small>
			</li>
		</ul>
		<h2>PostgreSQL</h2>
		<ul>
			<li>
				<a href="pgsql_simply.php">Simply</a><br />
				<small>(DBCommand without and with parameters; GetTable, ExecuteNonQuery methods)</small>
			</li>
			<li>
				<a href="pg_insert_get_id.php">Insert and get ID</a><br />
				<small>(DBCommand with parameters, ExecuteScalar method)</small>
			</li>
			<li>
				<a href="pg_connstr.php">Various connection settings</a>
			</li>
		</ul>
	</body>
</html>