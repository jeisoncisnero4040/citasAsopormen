import { useState, useMemo, useEffect } from "react";

const useDynamicTable = (initialData = [], rowsPerPage = 10, actionCallback = null) => {
  const [data, setData] = useState(initialData);
  const [currentPage, setCurrentPage] = useState(1);
  const [sortConfig, setSortConfig] = useState({ key: null, direction: "asc" });
  const [filterQuery, setFilterQuery] = useState("");

  useEffect(() => {
    setData(initialData);
  }, [initialData]);

  const sortedData = useMemo(() => {
    if (!sortConfig.key) return data;
    return [...data].sort((a, b) => {
      const valueA = a[sortConfig.key];
      const valueB = b[sortConfig.key];
      if (valueA < valueB) return sortConfig.direction === "asc" ? -1 : 1;
      if (valueA > valueB) return sortConfig.direction === "asc" ? 1 : -1;
      return 0;
    });
  }, [data, sortConfig]);

  const filteredData = useMemo(() => {
    return sortedData.filter((row) =>
      Object.values(row).some((value) =>
        value?.toString().toLowerCase().includes(filterQuery.toLowerCase())
      )
    );
  }, [sortedData, filterQuery]);

  const paginatedData = useMemo(() => {
    const startIndex = (currentPage - 1) * rowsPerPage;
    return filteredData.slice(startIndex, startIndex + rowsPerPage).map((row) => ({
      ...row,
      acciones: actionCallback
        ? (
          <button onClick={() => actionCallback(row)} style={{ cursor: "pointer", padding: "5px 10px", backgroundColor: "#007bff", color: "#fff", border: "none", borderRadius: "5px" }}>
            Acción
          </button>
        )
        : null,
    }));
  }, [filteredData, currentPage, rowsPerPage, actionCallback]);

  const sortBy = (key) => {
    setSortConfig((prev) => ({
      key,
      direction: prev.key === key && prev.direction === "asc" ? "desc" : "asc",
    }));
  };

  return {
    data: paginatedData,
    setData,
    currentPage,
    setCurrentPage,
    sortBy,
    filterQuery,
    setFilterQuery,
    totalPages: Math.ceil(filteredData.length / rowsPerPage),
  };
};

export default useDynamicTable;
