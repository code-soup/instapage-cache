import React from "react";
import ReactDOM from "react-dom/client";

const InstapageCacheManager = React.lazy(
	() => import("./react/components/manager/index"),
);

document.addEventListener("DOMContentLoaded", function () {
	const root = ReactDOM.createRoot(
		document.getElementById("instapage-cache-app"),
	);

	root.render(
		<React.Suspense fallback={<div>Loading...</div>}>
			<React.StrictMode>
				<InstapageCacheManager />
			</React.StrictMode>
		</React.Suspense>,
	);
});
