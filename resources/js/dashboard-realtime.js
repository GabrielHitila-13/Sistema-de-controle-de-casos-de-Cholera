class DashboardRealTime {
  constructor() {
    this.updateInterval = 15000 // 15 seconds
    this.charts = {}
    this.isUpdating = false
    this.lastUpdate = null
    this.retryCount = 0
    this.maxRetries = 3

    this.init()
  }

  init() {
    this.setupEventListeners()
    this.startPeriodicUpdates()
    this.setupVisibilityHandler()
  }

  setupEventListeners() {
    // Manual refresh button
    document.addEventListener("click", (e) => {
      if (e.target.matches('[data-action="refresh-dashboard"]')) {
        e.preventDefault()
        this.forceUpdate()
      }
    })

    // Auto-refresh toggle
    const autoRefreshToggle = document.getElementById("auto-refresh-toggle")
    if (autoRefreshToggle) {
      autoRefreshToggle.addEventListener("change", (e) => {
        if (e.target.checked) {
          this.startPeriodicUpdates()
        } else {
          this.stopPeriodicUpdates()
        }
      })
    }
  }

  setupVisibilityHandler() {
    // Pause updates when tab is not visible
    document.addEventListener("visibilitychange", () => {
      if (document.hidden) {
        this.stopPeriodicUpdates()
      } else {
        this.startPeriodicUpdates()
        this.forceUpdate() // Update immediately when tab becomes visible
      }
    })
  }

  startPeriodicUpdates() {
    if (this.updateTimer) {
      clearInterval(this.updateTimer)
    }

    this.updateTimer = setInterval(() => {
      this.updateDashboard()
    }, this.updateInterval)

    console.log("Dashboard real-time updates started")
  }

  stopPeriodicUpdates() {
    if (this.updateTimer) {
      clearInterval(this.updateTimer)
      this.updateTimer = null
    }

    console.log("Dashboard real-time updates stopped")
  }

  async forceUpdate() {
    await this.updateDashboard(true)
  }

  async updateDashboard(force = false) {
    if (this.isUpdating && !force) {
      return
    }

    this.isUpdating = true
    this.showUpdateIndicator()

    try {
      // Update all dashboard components
      await Promise.all([
        this.updateStats(),
        this.updateEvolutionChart(),
        this.updateDiagnosisChart(),
        this.updateRecentPatients(),
        this.updateAmbulanceData(),
      ])

      this.lastUpdate = new Date()
      this.retryCount = 0
      this.updateLastUpdateTime()

      console.log("Dashboard updated successfully")
    } catch (error) {
      console.error("Dashboard update failed:", error)
      this.handleUpdateError(error)
    } finally {
      this.isUpdating = false
      this.hideUpdateIndicator()
    }
  }

  async updateStats() {
    try {
      const response = await fetch("/api/dashboard/stats", {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json",
        },
      })

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`)
      }

      const data = await response.json()

      if (data.success) {
        this.updateStatsCards(data.data)
      }
    } catch (error) {
      console.error("Failed to update stats:", error)
      throw error
    }
  }

  async updateEvolutionChart() {
    try {
      const response = await fetch("/api/dashboard/evolution-data", {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json",
        },
      })

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`)
      }

      const data = await response.json()

      if (data.success && this.charts.evolution) {
        this.updateEvolutionChartData(data.data)
      }
    } catch (error) {
      console.error("Failed to update evolution chart:", error)
      throw error
    }
  }

  async updateDiagnosisChart() {
    try {
      const response = await fetch("/api/dashboard/diagnosis-data", {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json",
        },
      })

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`)
      }

      const data = await response.json()

      if (data.success && this.charts.diagnosis) {
        this.updateDiagnosisChartData(data.data)
      }
    } catch (error) {
      console.error("Failed to update diagnosis chart:", error)
      throw error
    }
  }

  async updateRecentPatients() {
    try {
      const response = await fetch("/api/dashboard/recent-patients", {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json",
        },
      })

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`)
      }

      const data = await response.json()

      if (data.success) {
        this.updateRecentPatientsTable(data.data)
      }
    } catch (error) {
      console.error("Failed to update recent patients:", error)
      throw error
    }
  }

  async updateAmbulanceData() {
    try {
      const response = await fetch("/api/dashboard/ambulance-data", {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json",
        },
      })

      if (!response.ok) {
        if (response.status === 403) {
          // User doesn't have permission, skip this update
          return
        }
        throw new Error(`HTTP ${response.status}`)
      }

      const data = await response.json()

      if (data.success) {
        this.updateAmbulanceChart(data.data)
        this.updateAmbulanceStats(data.data)
      }
    } catch (error) {
      console.error("Failed to update ambulance data:", error)
      throw error
    }
  }

  updateStatsCards(stats) {
    const updates = [
      { selector: '[data-stat="pacientes-total"]', value: stats.pacientes_total },
      { selector: '[data-stat="colera-confirmada"]', value: stats.colera_confirmada },
      { selector: '[data-stat="alto-risco"]', value: stats.pacientes_alto_risco },
      { selector: '[data-stat="veiculos-disponiveis"]', value: stats.veiculos_disponiveis },
    ]

    updates.forEach((update) => {
      const element = document.querySelector(update.selector)
      if (element && update.value !== undefined) {
        const currentValue = Number.parseInt(element.textContent.replace(/[^\d]/g, ""))
        const newValue = update.value

        if (currentValue !== newValue) {
          this.animateNumberChange(element, currentValue, newValue)
        }
      }
    })
  }

  updateEvolutionChartData(data) {
    const chart = this.charts.evolution
    if (!chart) return

    // Update chart data with animation
    chart.data.labels = data.labels
    chart.data.datasets[0].data = data.confirmados
    chart.data.datasets[1].data = data.provaveis
    chart.data.datasets[2].data = data.suspeitos

    chart.update("active")
  }

  updateDiagnosisChartData(data) {
    const chart = this.charts.diagnosis
    if (!chart) return

    // Update chart data
    chart.data.datasets[0].data = [data.confirmado, data.provavel, data.suspeito, data.descartado, data.pendente]

    chart.update("active")

    // Update legend numbers
    this.updateDiagnosisLegend(data)
  }

  updateDiagnosisLegend(data) {
    const updates = [
      { selector: '[data-diagnosis="confirmado"]', value: data.confirmado },
      { selector: '[data-diagnosis="provavel"]', value: data.provavel },
      { selector: '[data-diagnosis="suspeito"]', value: data.suspeito },
      { selector: '[data-diagnosis="descartado"]', value: data.descartado },
      { selector: '[data-diagnosis="pendente"]', value: data.pendente },
    ]

    updates.forEach((update) => {
      const element = document.querySelector(update.selector)
      if (element) {
        element.textContent = update.value
      }
    })
  }

  updateRecentPatientsTable(patients) {
    const container = document.querySelector('[data-component="recent-patients"]')
    if (!container) return

    if (patients.length === 0) {
      container.innerHTML = this.getEmptyPatientsHTML()
      return
    }

    const html = patients.map((patient) => this.getPatientRowHTML(patient)).join("")
    container.innerHTML = html
  }

  updateAmbulanceChart(data) {
    const chart = this.charts.ambulance
    if (!chart) return

    chart.data.datasets[0].data = [data.disponivel, data.em_atendimento, data.manutencao, data.indisponivel]

    chart.update("active")
  }

  updateAmbulanceStats(data) {
    const updates = [
      { selector: '[data-ambulance="disponivel"]', value: data.disponivel },
      { selector: '[data-ambulance="em-atendimento"]', value: data.em_atendimento },
      { selector: '[data-ambulance="manutencao"]', value: data.manutencao },
      { selector: '[data-ambulance="indisponivel"]', value: data.indisponivel },
    ]

    updates.forEach((update) => {
      const element = document.querySelector(update.selector)
      if (element) {
        element.textContent = update.value
      }
    })
  }

  animateNumberChange(element, from, to) {
    const duration = 1000
    const steps = 30
    const stepValue = (to - from) / steps
    let current = from
    let step = 0

    element.classList.add("updating")

    const timer = setInterval(() => {
      step++
      current += stepValue

      if (step >= steps) {
        current = to
        clearInterval(timer)
        element.classList.remove("updating")
      }

      element.textContent = Math.round(current).toLocaleString()
    }, duration / steps)
  }

  getPatientRowHTML(patient) {
    const riscoClass =
      patient.risco === "alto"
        ? "bg-red-100 text-red-800"
        : patient.risco === "medio"
          ? "bg-yellow-100 text-yellow-800"
          : "bg-green-100 text-green-800"

    const diagnosticoClass =
      patient.diagnostico_colera === "confirmado"
        ? "bg-red-100 text-red-800"
        : patient.diagnostico_colera === "provavel"
          ? "bg-yellow-100 text-yellow-800"
          : "bg-blue-100 text-blue-800"

    return `
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900">${patient.nome}</h4>
                    <p class="text-sm text-gray-600">${patient.estabelecimento?.nome || "N/A"}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${riscoClass}">
                        ${patient.risco_formatado}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${diagnosticoClass}">
                        ${patient.diagnostico_colera_formatado}
                    </span>
                </div>
            </div>
        `
  }

  getEmptyPatientsHTML() {
    return `
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500">Nenhum caso recente</p>
            </div>
        `
  }

  showUpdateIndicator() {
    const indicator = document.querySelector('[data-component="update-indicator"]')
    if (indicator) {
      indicator.classList.remove("hidden")
    }
  }

  hideUpdateIndicator() {
    const indicator = document.querySelector('[data-component="update-indicator"]')
    if (indicator) {
      indicator.classList.add("hidden")
    }
  }

  updateLastUpdateTime() {
    const timeElement = document.querySelector('[data-component="last-update"]')
    if (timeElement && this.lastUpdate) {
      timeElement.textContent = `Última atualização: ${this.lastUpdate.toLocaleTimeString()}`
    }
  }

  handleUpdateError(error) {
    this.retryCount++

    if (this.retryCount <= this.maxRetries) {
      console.log(`Retrying update in 5 seconds (attempt ${this.retryCount}/${this.maxRetries})`)
      setTimeout(() => {
        this.updateDashboard()
      }, 5000)
    } else {
      console.error("Max retries reached. Stopping automatic updates.")
      this.stopPeriodicUpdates()
      this.showErrorNotification()
    }
  }

  showErrorNotification() {
    // You can implement a toast notification here
    console.error("Dashboard updates failed. Please refresh the page.")
  }

  // Method to register charts
  registerChart(name, chart) {
    this.charts[name] = chart
  }

  // Cleanup method
  destroy() {
    this.stopPeriodicUpdates()
    this.charts = {}
  }
}

// Export for use in other scripts
window.DashboardRealTime = DashboardRealTime
