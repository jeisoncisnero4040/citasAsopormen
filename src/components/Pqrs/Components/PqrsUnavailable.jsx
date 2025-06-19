import React from "react";

const PqrsUnavailable = () => {
  return (
    <div style={styles.container}>
      <div style={styles.card}>
        <h1 style={styles.title}>PQRS no disponible</h1>
        <p style={styles.subtitle}>
          Esta solicitud ya fue respondida o no está disponible actualmente.
        </p>
      </div>
    </div>
  );
};

const styles = {
  container: {
    width: "100vw",
    height: "100vh",
    display: "flex" ,
    justifyContent: "center",
    alignItems: "center",
    backgroundColor: "#f9fafb",
  },
  card: {
    textAlign: "center" ,
    padding: "2rem",
    borderRadius: "1rem",
    backgroundColor: "#ffffff",
    boxShadow: "0 4px 14px rgba(0, 0, 0, 0.1)",
    maxWidth: "400px",
    width: "90%",
  },
  title: {
    fontSize: "1.8rem",
    marginBottom: "1rem",
    color: "#111827",
  },
  subtitle: {
    fontSize: "1rem",
    color: "#6b7280",
  },
};

export default PqrsUnavailable;
