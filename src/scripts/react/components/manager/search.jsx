import React, { useEffect, useState } from "react";

const Search = ({ onSearch }) => {
	const [search, setSearch] = useState("");

	const handleSearchChange = (event) => {
		setSearch(event.target.value);

		if ("" === event.target.value) {
			onSearch("");
		}
	};

	const doSearch = (event) => {
		event.preventDefault();
		onSearch(search);
	};

	const doReset = () => {
		event.preventDefault();
		setSearch("");
		onSearch("");
	};

	const isDisabled = () => {
		return search ? "" : " disabled";
	};

	return (
		<div id="items-search">
			<form>
				<div className="span-input">
					<input
						id="input-search-items"
						type="text"
						name="search-items"
						value={search}
						className="input-text"
						placeholder="Search &hellip;"
						onChange={handleSearchChange}
					/>
				</div>
				<div className="span-button">
					<button
						type="submit"
						onClick={doSearch}
						className="button button-primary"
					>
						Search
					</button>
					<button
						type="reset"
						onClick={doReset}
						disabled={isDisabled()}
						className="button button-secondary"
					>
						Clear
					</button>
				</div>
			</form>
		</div>
	);
};

export default Search;
